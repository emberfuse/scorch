<?php

namespace Cratespace\Sentinel\Contracts\Actions;

use Illuminate\Http\Request;

interface LogsoutUsers
{
    /**
     * Logout currently authenticated user.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return void
     */
    public function logout(Request $request): void;
}
