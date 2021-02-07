<?php

namespace Cratespace\Sentinel\Tests\Fixtures;

use Illuminate\Foundation\Auth\User;
use Cratespace\Sentinel\Models\Traits\TwoFactorAuthenticatable;

class TestTwoFactorAuthenticationUser extends User
{
    use TwoFactorAuthenticatable;

    protected $table = 'users';
}
