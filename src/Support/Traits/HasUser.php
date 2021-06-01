<?php

namespace Emberfuse\Scorch\Support\Traits;

use App\Models\User;
use InvalidArgumentException;

trait HasUser
{
    /**
     * Get the user instance.
     *
     * @return \App\Models\User
     *
     * @throws \InvalidArgumentException
     */
    public function getUser(): User
    {
        if (property_exists($this, 'user')) {
            return $this->user;
        }

        throw new InvalidArgumentException('Property `user` does not exist in this class');
    }
}
