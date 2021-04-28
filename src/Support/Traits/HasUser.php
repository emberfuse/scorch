<?php

namespace Cratespace\Sentinel\Support\Traits;

use LogicException;
use Illuminate\Contracts\Auth\Authenticatable as User;

trait HasUser
{
    /**
     * Get the user instance.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable
     *
     * @throws \LogicException
     */
    public function getUser(): User
    {
        if (property_exists($this, 'user')) {
            return $this->user;
        }

        throw new LogicException('Property `user` does not exist in this class');
    }
}
