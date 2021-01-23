<?php

namespace Citadel\Contracts\Actions;

use Illuminate\Contracts\Auth\Authenticatable;

interface UpdatesUserPasswords
{
    /**
     * Update the user's password.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     * @param array                                      $data
     *
     * @return void
     */
    public function update(Authenticatable $user, array $data): void;
}
