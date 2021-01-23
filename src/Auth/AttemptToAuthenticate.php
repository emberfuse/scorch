<?php

namespace Citadel\Auth;

use Illuminate\Http\Request;
use Citadel\Contracts\Auth\AuthenticatesUsers;

class AttemptToAuthenticate extends Authenticate implements AuthenticatesUsers
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
        if ($this->authenticate($request)) {
            return $next($request);
        }

        $this->throwFailedAuthenticationException($request);
    }

    /**
     * Authenticate user making current request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return bool
     */
    public function authenticate(Request $request): bool
    {
        return $this->attempt($request);
    }
}
