<?php

namespace Citadel\Tests;

use Citadel\Auth\Config;
use Citadel\Tests\TestCase;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Routing\ResponseFactory;
use Citadel\Providers\CitadelServiceProvider;

class AuthenticationTest extends TestCase
{
    public function test_user_can_authenticate()
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

    protected function getPackageProviders($app)
    {
        return [CitadelServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['migrator']->path(__DIR__.'/../database/migrations');

        $app['config']->set('auth.providers.users.model', TestAuthenticationSessionUser::class);

        $app['config']->set('database.default', 'testbench');

        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }
}

class TestAuthenticationSessionUser extends User
{
    protected $table = 'users';
}
