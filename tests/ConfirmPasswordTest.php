<?php

namespace Emberfuse\Scorch;

use Emberfuse\Scorch\Tests\TestCase;
use Emberfuse\Scorch\Tests\Traits\HasUserAttributes;
use Emberfuse\Scorch\Tests\Fixtures\TestConfirmPasswordUser;
use Emberfuse\Scorch\Contracts\Responses\ConfirmPasswordViewResponse;

class ConfirmPasswordTest extends TestCase
{
    use HasUserAttributes;

    /**
     * Instance of confirm password test user.
     *
     * @var \Illuminate\Foundation\Auth\User
     */
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->migrate();

        $this->user = TestConfirmPasswordUser::forceCreate($this->userDetails());
    }

    public function testTheConfirmPasswordViewIsReturned()
    {
        $this->mock(ConfirmPasswordViewResponse::class)
            ->shouldReceive('toResponse')
            ->andReturn(response('hello world'));

        $response = $this->withoutExceptionHandling()
            ->actingAs($this->user)
            ->get('/user/confirm-password');

        $response->assertStatus(200);
        $response->assertSeeText('hello world');
    }

    public function testPasswordCanBeConfirmed()
    {
        $response = $this->withoutExceptionHandling()
            ->actingAs($this->user)
            ->withSession(['url.intended' => 'http://foo.com/bar'])
            ->post('/user/confirm-password', ['password' => 'cthuluEmployee']);

        $response->assertSessionHas('auth.password_confirmed_at');
        $response->assertRedirect('http://foo.com/bar');
    }

    public function testPasswordConfirmationCanFailWithAnInvalidPassword()
    {
        $response = $this->withoutExceptionHandling()
            ->actingAs($this->user)
            ->withSession(['url.intended' => 'http://foo.com/bar'])
            ->post('/user/confirm-password', ['password' => 'invalid']);

        $response->assertSessionHasErrors(['password']);
        $response->assertSessionMissing('auth.password_confirmed_at');
        $response->assertRedirect();
        $this->assertNotEquals($response->getTargetUrl(), 'http://foo.com/bar');
    }

    public function testPasswordConfirmationCanFailWithoutAPassword()
    {
        $response = $this->actingAs($this->user)
            ->withSession(['url.intended' => 'http://foo.com/bar'])
            ->post('/user/confirm-password', ['password' => null]);

        $response->assertSessionHasErrors(['password']);
        $response->assertSessionMissing('auth.password_confirmed_at');
        $response->assertRedirect();
        $this->assertNotEquals($response->getTargetUrl(), 'http://foo.com/bar');
    }

    public function testPasswordCanBeConfirmedWithJson()
    {
        $response = $this->actingAs($this->user)
            ->postJson('/user/confirm-password', ['password' => 'cthuluEmployee']);

        $response->assertStatus(201);
    }

    public function testPasswordConfirmationCanFailWithJson()
    {
        $response = $this->actingAs($this->user)
            ->postJson('/user/confirm-password', ['password' => 'invalid']);

        $response->assertJsonValidationErrors('password');
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['migrator']->path(__DIR__ . '/../database/migrations');

        $app['config']->set('auth.providers.users.model', TestConfirmPasswordUser::class);

        $app['config']->set('database.default', 'testbench');

        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }
}
