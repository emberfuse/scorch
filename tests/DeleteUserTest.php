<?php

namespace Citadel\Tests;

use Mockery as m;
use Citadel\Contracts\Actions\DeletesUsers;
use Illuminate\Contracts\Auth\Authenticatable;

class DeleteUserTest extends testCase
{
    public function testUserCanBeDeleted()
    {
        $user = m::mock(Authenticatable::class);
        $deleter = $this->mock(DeletesUsers::class);
        $deleter->shouldReceive('delete')->with($user);

        $this->assertNull($deleter->delete($user));
    }
}
