<?php

namespace Emberfuse\Scorch\Contracts\Actions;

use App\Models\User;

interface DeletesUsers
{
    /**
     * Delete the given user.
     *
     * @param \App\Models\User $user
     *
     * @return void
     */
    public function delete(User $user): void;
}
