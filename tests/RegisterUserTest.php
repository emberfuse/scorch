<?php

namespace Emberfuse\Scorch\Tests;

use Mockery as m;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Auth\Authenticatable;
use Emberfuse\Scorch\Contracts\Actions\CreatesNewUsers;
use Emberfuse\Scorch\Contracts\Responses\RegisterViewResponse;

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

    public function testUsersCanBeCreatedAndRedirectedToIntendedUrl()
    {
        $this->mock(CreatesNewUsers::class)
            ->shouldReceive('create')
            ->andReturn(m::mock(Authenticatable::class));

        $this->mock(StatefulGuard::class)
            ->shouldReceive('login')
            ->once();

        $response = $this->withSession(['url.intended' => 'http://foo.com/bar'])
            ->post('/register', []);

        $response->assertRedirect('http://foo.com/bar');
    }
}
