<?php

namespace Emberfuse\Scorch\Auth;

use Emberfuse\Scorch\API\Tokens\PersonalAccessToken;
use Emberfuse\Scorch\API\Tokens\TransientToken;
use Emberfuse\Scorch\Models\Traits\HasApiTokens;
use Emberfuse\Scorch\Scorch\Config;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class Guard
{
    /**
     * The authentication factory implementation.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * The number of minutes tokens should be allowed to remain valid.
     *
     * @var int
     */
    protected $expiration;

    /**
     * The provider name.
     *
     * @var string
     */
    protected $provider;

    /**
     * Create a new guard instance.
     *
     * @param \Illuminate\Contracts\Auth\Factory $auth
     * @param int|null                           $expiration
     * @param string|null                        $provider
     *
     * @return void
     */
    public function __construct(AuthFactory $auth, ?int $expiration = null, ?string $provider = null)
    {
        $this->auth = $auth;
        $this->expiration = $expiration;
        $this->provider = $provider;
    }

    /**
     * Retrieve the authenticated user for the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function __invoke(Request $request)
    {
        foreach (Arr::wrap($this->defaultGuard('web')) as $guard) {
            if ($user = $this->auth->guard($guard)->user()) {
                return $this->supportsTokens($user)
                    ? $user->withAccessToken(new TransientToken())
                    : $user;
            }
        }

        if ($token = $request->bearerToken()) {
            $accessToken = PersonalAccessToken::findToken($token);

            if (
                ! $this->isValidAccessToken($accessToken) ||
                ! $this->supportsTokens($accessToken->tokenable)
            ) {
                return;
            }

            if (
                method_exists($accessToken->getConnection(), 'hasModifiedRecords') &&
                method_exists(
                    $accessToken->getConnection(),
                    'setRecordModificationState'
                )
            ) {
                tap($accessToken->getConnection()->hasModifiedRecords(), function (
                    bool $hasModifiedRecords
                ) use ($accessToken): void {
                    $accessToken->forceFill(['last_used_at' => now()])->save();

                    $accessToken->getConnection()
                        ->setRecordModificationState($hasModifiedRecords);
                });
            } else {
                $accessToken->forceFill(['last_used_at' => now()])->save();
            }

            return $accessToken->tokenable->withAccessToken($accessToken);
        }
    }

    /**
     * Get the currently authenticated user.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user(): ?Authenticatable
    {
        return $this->auth->guard($this->defaultGuard('web'))->user();
    }

    /**
     * Validate a user's credentials.
     *
     * @param array $credentials
     *
     * @return bool
     */
    public function validate(array $credentials = []): bool
    {
        return $this->expiration &&
            $credentials['accessToken']->created_at->lte(
                now()->subMinutes($this->expiration)
            );
    }

    /**
     * Determine if the tokenable model supports API tokens.
     *
     * @param mixed $tokenable
     *
     * @return bool
     */
    protected function supportsTokens($tokenable = null): bool
    {
        return $tokenable && in_array(
            HasApiTokens::class,
            class_uses_recursive(get_class($tokenable))
        );
    }

    /**
     * Determine if the provided access token is valid.
     *
     * @param mixed $accessToken
     *
     * @return bool
     */
    protected function isValidAccessToken($accessToken): bool
    {
        if (! $accessToken) {
            return false;
        }

        $isValid = (
            ! $this->expiration ||
            $accessToken->created_at->gt(now()->subMinutes($this->expiration))
        ) && (
            ! $accessToken->expires_at ||
            ! $accessToken->expires_at->isPast()
        ) && $this->hasValidProvider($accessToken->tokenable);

        return $isValid;
    }

    /**
     * Determine if the tokenable model matches the provider's model type.
     *
     * @param \Illuminate\Database\Eloquent\Model $tokenable
     *
     * @return bool
     */
    protected function hasValidProvider(Model $tokenable): bool
    {
        if (is_null($this->provider)) {
            return true;
        }

        $model = config("auth.providers.{$this->provider}.model");

        return $tokenable instanceof $model;
    }

    /**
     * Get default auth guard from configuration.
     *
     * @param string|null $default
     *
     * @return string|array
     */
    protected function defaultGuard(?string $default = null)
    {
        return Config::guard($default ?? 'web');
    }
}
