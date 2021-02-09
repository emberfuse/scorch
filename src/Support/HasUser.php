<?php

namespace Cratespace\Sentinel\Support;

use Illuminate\Contracts\Auth\Authenticatable as User;

trait HasUser
{
    /**
     * Get the user instance.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable
     */
    public function getUser(): User
    {
        return $this->user;
    }
}
