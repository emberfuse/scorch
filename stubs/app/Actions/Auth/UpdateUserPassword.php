<?php

namespace App\Actions\Auth;

use App\Models\User;
use App\Actions\Auth\Traits\PasswordUpdater;
use Cratespace\Sentinel\Contracts\Actions\UpdatesUserPasswords;

class UpdateUserPassword implements UpdatesUserPasswords
{
    use PasswordUpdater;

    /**
     * Update the user's password.
     *
     * @param \App\Models\User $user
     * @param array            $data
     *
     * @return void
     */
    public function update(User $user, array $data): void
    {
        $this->updatePassword($user, $data['password'], true);
    }
}
