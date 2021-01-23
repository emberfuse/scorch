<?php

namespace Citadel\Tests;

use Citadel\Citadel\Config;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Citadel\Actions\AuthenticateUser;
use Citadel\Limiters\LoginRateLimiter;
use Citadel\Contracts\Actions\AuthenticatesUsers;
use Citadel\Contracts\Responses\LoginViewResponse;
use Citadel\Tests\Fixtures\TestAuthenticationUser;

class AuthenticationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app->singleton(AuthenticatesUsers::class, AuthenticateUser::class);
    }

    public function testLoginViewResponseIsReturned()
    {
        $this->mock(LoginViewResponse::class)
            ->shouldReceive('toResponse')
            ->andReturn(response('login view'));

        $response = $this->withoutExceptionHandling()->get(route('login'));

        $response->assertStatus(200)->assertSeeText('login view');
    }

    public function testUserCanAuthenticate()
    {
        $this->loadLaravelMigrations(['--database' => 'testbench']);

        TestAuthenticationUser::forceCreate($this->userDetails());

        $response = $this->withoutExceptionHandling()->post('/login', [
            'email' => 'james.silverman@monster.com',
            'password' => 'cthuluEmployee',
        ]);

        $response->assertRedirect(Config::home());
    }

    public function testValidationExceptionReturnedOnFailure()
    {
        $this->loadLaravelMigrations(['--database' => 'testbench']);

        TestAuthenticationUser::forceCreate($this->userDetails());

        $response = $this->post('/login', [
            'email' => 'james.silverman@monster.com',
            'password' => 'cthuluHimself',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['email']);
    }

    public function testLoginAttemptsAreThrottled()
    {
        $this->mock(LoginRateLimiter::class, function ($mock) {
            $mock->shouldReceive('tooManyAttempts')->andReturn(true);
            $mock->shouldReceive('availableIn')->andReturn(10);
        });

        $response = $this->postJson('/login', [
            'email' => 'james.silverman@monster.com',
            'password' => 'cthuluEmployee',
        ]);

        $response->assertStatus(429);
        $response->assertJsonValidationErrors(['email']);
    }

    public function testTheUserCanLogoutOfTheApplication()
    {
        $this->loadLaravelMigrations(['--database' => 'testbench']);

        Auth::guard()->setUser(
            $user = TestAuthenticationUser::forceCreate($this->userDetails())
        );

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect('/');
        $this->assertNull(Auth::guard()->getUser());
    }

    public function testTheUserCanLogoutOfTheApplicationUsingJsonRequest()
    {
        $this->loadLaravelMigrations(['--database' => 'testbench']);

        Auth::guard()->setUser(
            $user = TestAuthenticationUser::forceCreate($this->userDetails())
        );

        $response = $this->actingAs($user)->postJson('/logout');

        $response->assertStatus(204);
        $this->assertNull(Auth::guard()->getUser());
    }

    /**
     * Array of faker user details.
     *
     * @return array
     */
    protected function userDetails(): array
    {
        return [
            'name' => 'James Silverman',
            'email' => 'james.silverman@monster.com',
            'password' => Hash::make('cthuluEmployee'),
        ];
    }
}
