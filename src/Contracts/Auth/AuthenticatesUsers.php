<?php

namespace Cratespace\Citadel\Contracts\Auth;

use Illuminate\Http\Request;

interface AuthenticatesUsers
{
    /**
     * Authenticate user making current request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return bool
     */
    public function authenticate(Request $request): bool;
}
