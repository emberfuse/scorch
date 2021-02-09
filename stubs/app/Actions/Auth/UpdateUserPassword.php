<?php

namespace App\Actions\Auth;

use App\Actions\Auth\Traits\PasswordUpdater;
use Illuminate\Contracts\Auth\Authenticatable;
use Cratespace\Sentinel\Contracts\Actions\UpdatesUserPasswords;

class UpdateUserPassword implements UpdatesUserPasswords
{
    use PasswordUpdater;

    /**
     * Update the user's password.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     * @param array                                      $data
     *
     * @return void
     */
    public function update(Authenticatable $user, array $data): void
    {
        $this->updatePassword($user, $data['password'], true);
    }
}
