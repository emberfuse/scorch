<?php

namespace Emberfuse\Scorch\Tests;

use Mockery as m;
use App\Models\User;
use Emberfuse\Scorch\Contracts\Actions\DeletesUsers;

class DeleteUserTest extends testCase
{
    public function testUserCanBeDeleted()
    {
        $user = m::mock(User::class);
        $deleter = $this->mock(DeletesUsers::class);
        $deleter->shouldReceive('delete')->with($user);

        $this->assertNull($deleter->delete($user));
    }
}
