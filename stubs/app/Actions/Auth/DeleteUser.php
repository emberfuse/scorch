<?php

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Cratespace\Sentinel\Contracts\Actions\DeletesUsers;
use Illuminate\Contracts\Auth\Authenticatable;

class DeleteUser implements DeletesUsers
{
    /**
     * Delete the given user.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     *
     * @return void
     */
    public function delete(Authenticatable $user): void
    {
        DB::transaction(function () use ($user) {
            tap($user, function (User $user) {
                $this->deleteUserResources($user);

                $this->deleteUserProfiles($user);
            })->delete();
        }, 2);
    }

    /**
     * Delete all resources that belong to the user.
     *
     * @param \App\Models\User $user
     *
     * @return void
     */
    protected function deleteUserResources(User $user): void
    {
    }

    /**
     * Delete user profile details.
     *
     * @param \App\Models\User $user
     *
     * @return void
     */
    protected function deleteUserProfiles(User $user): void
    {
        $user->deleteProfilePhoto();

        $user->tokens->each->delete();
    }
}
