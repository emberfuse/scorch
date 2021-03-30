<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Cratespace\Sentinel\Auth\Authenticate;

class DenyLockedAccount extends Authenticate
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
        $user = $this->getAttemptingUser($request);

        if (! $user->locked) {
            return $next($request);
        }

        $this->throwFailedAuthenticationException($request, $user);
    }
}
