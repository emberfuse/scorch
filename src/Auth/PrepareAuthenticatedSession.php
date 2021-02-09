<?php

namespace Cratespace\Sentinel\Auth;

use Closure;
use Illuminate\Http\Request;

class PrepareAuthenticatedSession extends Authenticate
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
        $request->session()->regenerate();

        $this->limiter->clear($request);

        return $next($request);
    }
}
