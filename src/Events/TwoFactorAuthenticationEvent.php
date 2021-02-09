<?php

namespace Cratespace\Sentinel\Events;

use Illuminate\Foundation\Auth\User;
use Cratespace\Sentinel\Support\HasUser;
use Illuminate\Foundation\Events\Dispatchable;

abstract class TwoFactorAuthenticationEvent
{
    use Dispatchable;
    use HasUser;

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
}
