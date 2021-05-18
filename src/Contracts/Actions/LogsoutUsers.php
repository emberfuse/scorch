<?php

namespace Cratespace\Sentinel\Contracts\Actions;

use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\StatefulGuard;

interface LogsoutUsers
{
    /**
     * Logout currently authenticated user.
     *
     * @param \Illuminate\Http\Request                 $request
     * @param \Illuminate\Contracts\Auth\StatefulGuard $guard
     *
     * @return void
     */
    public function logout(Request $request, StatefulGuard $guard): void;
}
