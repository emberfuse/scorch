<?php

namespace Cratespace\Sentinel\Tests;

use Mockery as m;
use App\Models\User;
use Cratespace\Sentinel\Contracts\Actions\UpdatesUserPasswords;

class UpdatePasswordTest extends TestCase
{
    public function testPasswordsCanBeUpdated()
    {
        $user = m::mock(User::class);

        $updater = $this->mock(UpdatesUserPasswords::class);
        $updater->shouldReceive('update')
            ->with($user, m::type('array'));

        $result = $updater->update($user, [
            'current_password' => 'password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $this->assertNull($result);
    }
}
