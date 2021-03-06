<?php

namespace Emberfuse\Scorch\Auth;

use Illuminate\Http\Request;
use Illuminate\Auth\Events\Failed;
use Illuminate\Foundation\Auth\User;
use Emberfuse\Scorch\Scorch\Config;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Validation\ValidationException;
use Emberfuse\Scorch\Limiters\LoginRateLimiter;
use Emberfuse\Scorch\Contracts\Actions\AuthenticatesUsers;
use Illuminate\Contracts\Container\BindingResolutionException;
use Emberfuse\Scorch\Support\Concerns\InteractsWithContainer;

abstract class Authenticate
{
    use InteractsWithContainer;

    /**
     * The guard implementation.
     *
     * @var \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected $guard;

    /**
     * The login rate limiter instance.
     *
     * @var \Scorch\Limiters\LoginRateLimiter
     */
    protected $limiter;

    /**
     * Create a new controller instance.
     *
     * @param \Illuminate\Contracts\Auth\StatefulGuard $guard
     * @param \Emberfuse\Scorch\Limiters\LoginRateLimiter      $limiter
     *
     * @return void
     */
    public function __construct(StatefulGuard $guard, LoginRateLimiter $limiter)
    {
        $this->guard = $guard;
        $this->limiter = $limiter;
    }

    /**
     * Make an attempt to authenticate user making request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return bool
     */
    public function attempt(Request $request): bool
    {
        try {
            return $this->resolve(AuthenticatesUsers::class)->authenticate($request);
        } catch (BindingResolutionException $e) {
            return $this->guard->attempt(
                $request->only($this->username(), 'password'),
                $request->filled('remember')
            );
        }
    }

    /**
     * Attempt to validate the incoming credentials.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    protected function validateCredentials(Request $request)
    {
        return tap(
            $this->getAttemptingUser($request),
            function (?Authenticatable $user = null) use ($request) {
                if (! $user || ! $this->getProvider()->validateCredentials($user, ['password' => $request->password])) {
                    $this->throwFailedAuthenticationException($request);
                }
            }
        );
    }

    /**
     * Get instance of authenticatable user.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Foundation\Auth\User|null
     */
    protected function getAttemptingUser(Request $request): ?User
    {
        return ($this->getAuthModel())::where(
            $this->username(),
            $request->{$this->username()}
        )->first();
    }

    /**
     * Get default authenticatable user model.
     *
     * @return string
     */
    protected function getAuthModel(): string
    {
        return $this->getProvider()->getModel();
    }

    /**
     * Get the user provider used by the guard.
     *
     * @return \Illuminate\Contracts\Auth\UserProvider
     */
    protected function getProvider(): UserProvider
    {
        return $this->guard->getProvider();
    }

    /**
     * Throw a failed authentication validation exception.
     *
     * @param \Illuminate\Http\Request              $request
     * @param \Illuminate\Auth\Authenticatable|null $user
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function throwFailedAuthenticationException(
        Request $request,
        ?Authenticatable $user = null
    ): void {
        $this->limiter->increment($request);

        $this->fireFailedEvent($request, $user);

        throw ValidationException::withMessages([$this->username() => [trans('auth.failed')]]);
    }

    /**
     * Fire the failed authentication attempt event with the given arguments.
     *
     * @param \Illuminate\Http\Request              $request
     * @param \Illuminate\Auth\Authenticatable|null $user
     *
     * @return void
     */
    protected function fireFailedEvent(Request $request, ?Authenticatable $user = null): void
    {
        event(new Failed(Config::guard(), $user, [
            $this->username() => $request->{$this->username()},
            'password' => $request->password,
        ]));
    }

    /**
     * Get default user attribute treated as username.
     *
     * @return string
     */
    protected function username(): string
    {
        return Config::username(['email']);
    }
}
