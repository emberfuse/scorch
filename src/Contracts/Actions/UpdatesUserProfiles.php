<?php

namespace Cratespace\Sentinel\Contracts\Actions;

use Illuminate\Contracts\Auth\Authenticatable;

interface UpdatesUserProfiles
{
    /**
     * Validate and update the given user's profile information.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     * @param array                                      $data
     *
     * @return void
     */
    public function update(Authenticatable $user, array $data): void;
}
