<?php

namespace Cratespace\Sentinel\Actions;

use Illuminate\Http\Request;
use Illuminate\Contracts\Session\Session;
use Illuminate\Contracts\Auth\StatefulGuard;

class LogoutUser
{
    /**
     * Logout currently authenticated user.
     *
     * @param \Illuminate\Http\Request                 $request
     * @param \Illuminate\Contracts\Auth\StatefulGuard $guard
     *
     * @return void
     */
    public function logout(Request $request, StatefulGuard $guard): void
    {
        $guard->logout();

        tap($request->session(), function (Session $session): void {
            $session->invalidate();

            $session->regenerateToken();
        });
    }
}
