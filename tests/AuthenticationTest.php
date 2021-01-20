<?php

namespace Citadel\Tests;

use Mockery;
use Citadel\Auth\Config;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Citadel\Limiters\LoginRateLimiter;
use Citadel\Providers\CitadelServiceProvider;
use Illuminate\Contracts\Auth\Authenticatable;

class AuthenticationTest extends TestCase
{
    public function testUserCanAuthenticate()
    {
        $this->loadLaravelMigrations(['--database' => 'testbench']);

        TestAuthenticationSessionUser::forceCreate([
            'name' => 'Thavarshan Thayananthajothy',
            'email' => 'thavarshan@citadel.com',
            'password' => Hash::make('citadelrocks'),
        ]);

        $response = $this->withoutExceptionHandling()
            ->post('/login', [
                'email' => 'thavarshan@citadel.com',
                'password' => 'citadelrocks',
            ]);

        $response->assertRedirect(Config::home());
    }

    public function testValidationExceptionReturnedOnFailure()
    {
        $this->loadLaravelMigrations(['--database' => 'testbench']);

        TestAuthenticationSessionUser::forceCreate([
            'name' => 'Thavarshan Thayananthajothy',
            'email' => 'thavarshan@citadel.com',
            'password' => Hash::make('citadelrocks'),
        ]);

        $response = $this->post('/login', [
            'email' => 'thavarshan@citadel.com',
            'password' => 'citadeldoesnotrock',
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
            'email' => 'thavarshan@citadel.com',
            'password' => 'citadeldoesnotrock',
        ]);

        $response->assertStatus(429);
        $response->assertJsonValidationErrors(['email']);
    }

    public function testTheUserCanLogoutOfTheApplication()
    {
        Auth::guard()->setUser(
            Mockery::mock(Authenticatable::class)->shouldIgnoreMissing()
        );

        $response = $this->post('/logout');

        $response->assertRedirect('/');
        $this->assertNull(Auth::guard()->getUser());
    }

    public function testTheUserCanLogoutOfTheApplicationUsingJsonRequest()
    {
        Auth::guard()->setUser(
            Mockery::mock(Authenticatable::class)->shouldIgnoreMissing()
        );

        $response = $this->postJson('/logout');

        $response->assertStatus(204);
        $this->assertNull(Auth::guard()->getUser());
    }

    protected function getPackageProviders($app)
    {
        return [CitadelServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['migrator']->path(__DIR__ . '/../database/migrations');

        $app['config']->set('auth.providers.users.model', TestAuthenticationSessionUser::class);

        $app['config']->set('database.default', 'testbench');

        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }
}

class TestAuthenticationSessionUser extends User
{
    protected $table = 'users';
}
