<?php

namespace Citadel\Contracts;

interface AuthenticatesUsers
{
    /**
     * Authenticate user making current request.
     *
     * @param array $data
     * @return bool
     */
    public function authenticate(array $data): bool;
}
