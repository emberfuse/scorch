<?php

namespace Cratespace\Sentinel\Tests;

use Mockery as m;
use Cratespace\Sentinel\Contracts\Actions\DeletesUsers;
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
