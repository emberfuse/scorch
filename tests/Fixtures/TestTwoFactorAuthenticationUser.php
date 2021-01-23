<?php

namespace Citadel\Tests\Fixtures;

use Illuminate\Foundation\Auth\User;
use Citadel\Auth\TwoFactorAuthenticatable;

class TestTwoFactorAuthenticationUser extends User
{
    use TwoFactorAuthenticatable;

    protected $table = 'users';
}
