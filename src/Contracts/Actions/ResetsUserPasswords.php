<?php

namespace Citadel\Contracts\Actions;

use Illuminate\Http\Request;

interface ResetsUserPasswords
{
    /**
     * Validate and reset the user's forgotten password.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function reset(Request $request);
}
