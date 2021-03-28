<?php

namespace Cratespace\Sentinel\Contracts\Actions;

use App\Models\User;

interface UpdatesUserPasswords
{
    /**
     * Update the user's password.
     *
     * @param \App\Models\User $user
     * @param array            $data
     *
     * @return void
     */
    public function update(User $user, array $data): void;
}
