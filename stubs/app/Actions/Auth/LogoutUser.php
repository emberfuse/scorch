<?php

namespace App\Actions\Auth;

use Illuminate\Http\Request;
use Illuminate\Contracts\Session\Session;
use Illuminate\Contracts\Auth\StatefulGuard;
use Cratespace\Sentinel\Contracts\Actions\LogsoutUsers;

class LogoutUser implements LogsoutUsers
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
