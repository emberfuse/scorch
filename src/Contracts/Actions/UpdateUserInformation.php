<?php

namespace Emberfuse\Scorch\Contracts\Actions;

use App\Models\User;

interface UpdateUserInformation
{
    /**
     * Validate and update the given user's profile information.
     *
     * @param \App\Models\User $user
     * @param array            $data
     * @param array|null       $options
     *
     * @return void
     */
    public function update(User $user, array $data, ?array $options = null): void;
}
