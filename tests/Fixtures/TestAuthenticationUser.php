<?php

namespace Citadel\Tests\Fixtures;

use Illuminate\Foundation\Auth\User;

class TestAuthenticationUser extends User
{
    protected $table = 'users';
}
