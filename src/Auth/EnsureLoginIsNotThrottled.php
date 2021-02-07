<?php

namespace Cratespace\Sentinel\Auth;

use Illuminate\Auth\Events\Lockout;
use Cratespace\Sentinel\Http\Responses\LockoutResponse;

class EnsureLoginIsNotThrottled extends Authenticate
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param callable                 $next
     *
     * @return mixed
     */
    public function handle($request, $next)
    {
        if (! $this->limiter->tooManyAttempts($request)) {
            return $next($request);
        }

        event(new Lockout($request));

        return app(LockoutResponse::class);
    }
}
