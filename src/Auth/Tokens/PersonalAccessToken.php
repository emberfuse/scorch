<?php

namespace Cratespace\Sentinel\Auth\Tokens;

use Cratespace\Sentinel\Contracts\Auth\Access;
use Cratespace\Sentinel\Models\PersonalAccessToken as PersonalAccessTokenModel;

class PersonalAccessToken extends PersonalAccessTokenModel implements Access
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
        return in_array('*', $this->abilities) ||
            array_key_exists($ability, array_flip($this->abilities));
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
        return ! $this->can($ability);
    }
}
