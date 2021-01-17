<?php

namespace Citadel\Http\Middleware;

use Closure;
use Citadel\Auth\Config;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Failed;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Hash;
use Citadel\Limiters\LoginRateLimiter;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Validation\ValidationException;

abstract class Authenticate
{
    /**
     * The guard implementation.
     *
     * @var \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected $guard;

    /**
     * The login rate limiter instance.
     *
     * @var \Citadel\Limiters\LoginRateLimiter
     */
    protected $limiter;

    /**
     * Callback that will be executed after a user has been authenticated.
     *
     * @var \Closure|null
     */
    protected static $authenticated;

    /**
     * Create a new controller instance.
     *
     * @param \Illuminate\Contracts\Auth\StatefulGuard $guard
     * @param \Citadel\Limiters\LoginRateLimiter        $limiter
     *
     * @return void
     */
    public function __construct(StatefulGuard $guard, LoginRateLimiter $limiter)
    {
        $this->guard = $guard;
        $this->limiter = $limiter;
    }

    /**
     * Attempt to validate the incoming credentials.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateCredentials(Request $request)
    {
        return tap(
            $this->getAttemptingUser($request),
            function (?User $user = null) use ($request) {
                if (! $user || ! Hash::check($request->password, $user->password)) {
                    $this->fireFailedEvent($request, $user);

                    $this->throwFailedAuthenticationException($request);
                }
            }
        );
    }

    /**
     * Get instance of user model attempting authentication.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \App\Models\User|null
     */
    protected function getAttemptingUser(Request $request): ?User
    {
        return ($this->getAuthModel())::where(
            $this->username(),
            $request->{$this->username()}
        )->first();
    }

    /**
     * Get user model being used for authentication purposes.
     *
     * @return string
     */
    protected function getAuthModel(): string
    {
        return $this->guard->getProvider()->getModel();
    }

    /**
     * Throw a failed authentication validation exception.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function throwFailedAuthenticationException(Request $request): void
    {
        $this->limiter->increment($request);

        throw ValidationException::withMessages([$this->username() => [trans('auth.failed')]]);
    }

    /**
     * Fire the failed authentication attempt event with the given arguments.
     *
     * @param \Illuminate\Http\Request                        $request
     * @param \Illuminate\Contracts\Auth\Authenticatable|null $user
     *
     * @return void
     */
    protected function fireFailedEvent(Request $request, ?Authenticatable $user = null): void
    {
        event(new Failed(config('auth.defaults.guard'), $user, [
            $this->username() => $request->{$this->username()},
            'password' => $request->password,
        ]));
    }

    /**
     * Get the username used for authentication.
     *
     * @return string
     */
    public function username(): string
    {
        return Config::username();
    }

    /**
     * Register a callback that will be executed after a user has been authenticated.
     *
     * @param \Closure|null $callback
     *
     * @return void
     */
    public static function afterAuthentication(?Closure $callback = null): void
    {
        static::$authenticated = $callback;
    }
}
