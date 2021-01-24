<?php

namespace Citadel\Tests\Fixtures;

use Illuminate\Foundation\Auth\User;
use Citadel\Models\Traits\TwoFactorAuthenticatable;

class TestTwoFactorAuthenticationUser extends User
{
    use TwoFactorAuthenticatable;

    protected $table = 'users';
}
