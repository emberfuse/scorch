<?php

namespace Citadel\Actions;

use Illuminate\Support\Arr;
use Citadel\Contracts\AuthenticatesUsers;
use Illuminate\Contracts\Auth\StatefulGuard;

class AuthenticateUser implements AuthenticatesUsers
{
    /**
     * The guard implementation.
     *
     * @var \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected $guard;

    /**
     * Create new instance of authenticator action.
     *
     * @param \Illuminate\Contracts\Auth\StatefulGuard $guard
     * @return void
     */
    public function __construct(StatefulGuard $guard)
    {
        $this->guard = $guard;
    }

    /**
     * Authenticate user making current request.
     *
     * @param array $data
     * @return bool
     */
    public function authenticate(array $data): bool
    {
        return $this->guard->attempt(
            $this->credentials($data),
            isset($data['remember'])
        );
    }

    /**
     * Get only user credentials wrapped in array.
     *
     * @param array $data
     * @return array
     */
    protected function credentials(array $data): array
    {
        return Arr::only($data, [$this->username(), 'password']);
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username(): string
    {
        return config('citadel.username', 'email');
    }
}
