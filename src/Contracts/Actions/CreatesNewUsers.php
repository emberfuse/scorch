<?php

namespace Cratespace\Citadel\Contracts\Actions;

use Illuminate\Contracts\Auth\Authenticatable as User;

interface CreatesNewUsers
{
    /**
     * Create a newly registered user.
     *
     * @param array $data
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable
     */
    public function create(array $data): User;
}
