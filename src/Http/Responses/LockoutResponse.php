<?php

namespace Emberfuse\Scorch\Http\Responses;

use Emberfuse\Scorch\Limiters\LoginRateLimiter;
use Emberfuse\Scorch\Scorch\Config;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class LockoutResponse extends Response implements Responsable
{
    /**
     * The login rate limiter instance.
     *
     * @var \Emberfuse\Scorch\Limiters\LoginRateLimiter
     */
    protected $limiter;

    /**
     * Create a new response instance.
     *
     * @param \Emberfuse\Scorch\Limiters\LoginRateLimiter $limiter
     *
     * @return void
     */
    public function __construct(LoginRateLimiter $limiter)
    {
        $this->limiter = $limiter;
    }

    /**
     * Create an HTTP response that represents the object.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        return with($this->limiter->availableIn($request), function ($seconds) {
            throw ValidationException::withMessages([Config::username() => [trans('auth.throttle', ['seconds' => $seconds, 'minutes' => ceil($seconds / 60)])]])->status(Response::HTTP_TOO_MANY_REQUESTS);
        });
    }
}
