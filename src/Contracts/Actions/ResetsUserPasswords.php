<?php

namespace Citadel\Contracts\Actions;

interface ResetsUserPasswords
{
    /**
     * Validate and reset the user's forgotten password.
     *
     * @param array $data
     *
     * @return mixed
     */
    public function reset(array $data);
}
