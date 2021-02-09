<?php

namespace Cratespace\Sentinel\Tests\Fixtures;

use Illuminate\Foundation\Auth\User;
use Cratespace\Sentinel\Models\Traits\HasApiTokens;

class TestAuthenticationUser extends User
{
    use HasApiTokens;

    protected $table = 'users';
}
