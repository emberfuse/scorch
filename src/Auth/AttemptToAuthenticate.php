<?php

namespace Cratespace\Citadel\Auth;

class AttemptToAuthenticate extends Authenticate
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
        if ($this->attempt($request)) {
            return $next($request);
        }

        $this->throwFailedAuthenticationException($request);
    }
}
