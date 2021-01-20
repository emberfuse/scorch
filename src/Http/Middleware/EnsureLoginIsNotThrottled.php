<?php

namespace Citadel\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Lockout;
use Citadel\Http\Responses\LockoutResponse;

class EnsureLoginIsNotThrottled extends Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (! $this->limiter->tooManyAttempts($request)) {
            return $next($request);
        }

        event(new Lockout($request));

        return app(LockoutResponse::class);
    }
}
