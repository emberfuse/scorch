<?php

namespace Cratespace\Sentinel\Tests\Fixtures;

use Illuminate\Foundation\Auth\User;

class TestTwoFactorRecoveryCodeUser extends User
{
    protected $table = 'users';
}
