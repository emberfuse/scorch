<?php

namespace Emberfuse\Scorch\Contracts\Auth;

interface Access
{
    /**
     * Determine if the token has a given ability.
     *
     * @param string $ability
     *
     * @return bool
     */
    public function can(string $ability): bool;

    /**
     * Determine if the token is missing a given ability.
     *
     * @param string $ability
     *
     * @return bool
     */
    public function cant(string $ability): bool;
}
