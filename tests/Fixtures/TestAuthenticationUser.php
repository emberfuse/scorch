<?php

namespace Emberfuse\Scorch\Tests\Fixtures;

use Illuminate\Foundation\Auth\User;
use Emberfuse\Scorch\Models\Traits\HasApiTokens;

class TestAuthenticationUser extends User
{
    use HasApiTokens;

    protected $table = 'users';
}
