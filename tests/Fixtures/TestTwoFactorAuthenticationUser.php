<?php

namespace Cratespace\Citadel\Tests\Fixtures;

use Illuminate\Foundation\Auth\User;
use Cratespace\Citadel\Models\Traits\TwoFactorAuthenticatable;

class TestTwoFactorAuthenticationUser extends User
{
    use TwoFactorAuthenticatable;

    protected $table = 'users';
}
