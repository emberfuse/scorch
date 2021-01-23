<?php

namespace Citadel\Events;

use Illuminate\Foundation\Auth\User;
use Illuminate\Foundation\Events\Dispatchable;

abstract class TwoFactorAuthenticationEvent
{
    use Dispatchable;

    /**
     * The user instance.
     *
     * @var \Illuminate\Foundation\Auth\User
     */
    protected $user;

    /**
     * Create a new event instance.
     *
     * @param \Illuminate\Foundation\Auth\User $user
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

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
