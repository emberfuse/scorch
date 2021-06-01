<?php

namespace Emberfuse\Scorch\API\Tokens;

use Emberfuse\Scorch\Contracts\Auth\Access;

class TransientToken implements Access
{
    /**
     * Determine if the token has a given ability.
     *
     * @param string $ability
     *
     * @return bool
     */
    public function can(string $ability): bool
    {
        return true;
    }

    /**
     * Determine if the token is missing a given ability.
     *
     * @param string $ability
     *
     * @return bool
     */
    public function cant(string $ability): bool
    {
        return false;
    }
}
