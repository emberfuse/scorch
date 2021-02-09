<?php

namespace Cratespace\Sentinel\Tests\Traits;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

trait HasUserAttributes
{
    /**
     * Array of faker user details.
     *
     * @param array[] $overrides
     *
     * @return array
     */
    protected function userDetails(array $overrides = []): array
    {
        return array_merge([
            'name' => 'James Silverman',
            'username' => 'SilverJames',
            'email' => 'james.silverman@monster.com',
            'password' => Hash::make('cthuluEmployee'),
            'remember_token' => Str::random(10),
        ], $overrides);
    }
}
