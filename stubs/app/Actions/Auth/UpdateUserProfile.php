<?php

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Cratespace\Sentinel\Contracts\Actions\UpdatesUserProfiles;

class UpdateUserProfile implements UpdatesUserProfiles
{
    /**
     * Validate and update the given user's profile information.
     *
     * @param \App\Models\User $user
     * @param array            $data
     *
     * @return void
     */
    public function update(User $user, array $data): void
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
     * @param \App\Models\User $user
     * @param array            $data
     * @param bool             $verified
     *
     * @return void
     */
    protected function updateInformation(User $user, array $data, bool $verified = true): void
    {
        $user->forceFill(array_merge([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
        ], $verified ? ['email_verified_at' => null] : []))->save();

        if ($this->hasAddressInformation($data)) {
            $user->forceFill([
                'address' => [
                    'line1' => $data['line1'],
                    'line2' => $data['line2'] ?? null,
                    'city' => $data['city'],
                    'state' => $data['state'],
                    'country' => $data['country'],
                    'postal_code' => $data['postal_code'],
                ],
            ])->saveQuietly();
        }
    }

    /**
     * Determine if the given keys are included in the data array.
     *
     * @param array $data
     *
     * @return bool
     */
    protected function hasAddressInformation(array $data): bool
    {
        return collect($data)->contains(function ($value, $key): bool {
            return in_array($key, [
                'line1', 'line2', 'city', 'state', 'country', 'postal_code',
            ]);
        });
    }
}
