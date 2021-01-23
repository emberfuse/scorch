<?php

namespace Citadel\Contracts\Actions;

use Illuminate\Foundation\Auth\User;

interface CreatesNewUsers
{
    /**
     * Create a newly registered user.
     *
     * @param array $data
     *
     * @return \Illuminate\Foundation\Auth\User
     */
    public function create(array $data): User;
}
