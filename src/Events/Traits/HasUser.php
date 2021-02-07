<?php

namespace Cratespace\Sentinel\Events\Traits;

use Illuminate\Foundation\Auth\User;

trait HasUser
{
    /**
     * Get the user instance.
     *
     * @return \Illuminate\Foundation\Auth\User
     */
    public function getUser(): User
    {
        return $this->user;
    }
}
