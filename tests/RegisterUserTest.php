<?php

namespace Cratespace\Citadel\Tests;

use Mockery as m;
use Illuminate\Contracts\Auth\StatefulGuard;
use Cratespace\Citadel\Contracts\Actions\CreatesNewUsers;
use Illuminate\Contracts\Auth\Authenticatable;
use Cratespace\Citadel\Contracts\Responses\RegisterViewResponse;

class RegisterUserTest extends TestCase
{
    public function testTheRegisterViewIsReturned()
    {
        $this->mock(RegisterViewResponse::class)
            ->shouldReceive('toResponse')
            ->andReturn(response('hello world'));

        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertSeeText('hello world');
    }

    public function testUsersCanBeCreated()
    {
        $this->withoutExceptionHandling();

        $this->mock(CreatesNewUsers::class)
            ->shouldReceive('create')
            ->andReturn(m::mock(Authenticatable::class));

        $this->mock(StatefulGuard::class)
            ->shouldReceive('login')
            ->once();

        $response = $this->post('/register', []);

        $response->assertRedirect('/home');
    }
}
