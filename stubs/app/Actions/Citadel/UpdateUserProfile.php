<?php

namespace App\Actions\Citadel;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Cratespace\Citadel\Contracts\Actions\UpdatesUserProfiles;

class UpdateUserProfile implements UpdatesUserProfiles
{
    /**
     * Validate and update the given user's profile information.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     * @param array                                      $data
     *
     * @return void
     */
    public function update(Authenticatable $user, array $data): void
    {
        if (isset($data['photo'])) {
            $user->updateProfilePhoto($data['photo']);
        }

        if ($data['email'] !== $user->email && $user instanceof MustVerifyEmail) {
            $this->updateInformation($user, $data, true);

            $user->sendEmailVerificationNotification();
        } else {
            $this->updateInformation($user, $data, false);
        }
    }

    /**
     * Update the given user's profile information.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     * @param array                                      $data
     * @param bool                                       $verified
     *
     * @return void
     */
    protected function updateInformation(Authenticatable $user, array $data, bool $verified = true): void
    {
        $user->forceFill(array_merge([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
        ], $verified ? ['email_verified_at' => null] : []))->save();
    }
}
