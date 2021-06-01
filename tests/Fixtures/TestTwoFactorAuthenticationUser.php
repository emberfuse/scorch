<?php

namespace Emberfuse\Scorch\Tests\Fixtures;

use Illuminate\Foundation\Auth\User;
use Emberfuse\Scorch\Models\Traits\TwoFactorAuthenticatable;

class TestTwoFactorAuthenticationUser extends User
{
    use TwoFactorAuthenticatable;

    protected $table = 'users';
}
