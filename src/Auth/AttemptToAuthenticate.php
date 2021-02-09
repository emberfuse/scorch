<?php

namespace Cratespace\Sentinel\Auth;

use Closure;
use Illuminate\Http\Request;

class AttemptToAuthenticate extends Authenticate
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($this->attempt($request)) {
            return $next($request);
        }

        $this->throwFailedAuthenticationException($request);
    }
}
