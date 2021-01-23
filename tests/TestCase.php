<?php

namespace Citadel\Tests;

use Mockery as m;
use Citadel\Providers\CitadelServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Citadel\Tests\Fixtures\TestAuthenticationUser;

abstract class TestCase extends BaseTestCase
{
    public function tearDown(): void
    {
        m::close();
    }

    protected function getPackageProviders($app)
    {
        return [CitadelServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['migrator']->path(__DIR__ . '/../database/migrations');

        $app['config']->set('auth.providers.users.model', TestAuthenticationUser::class);

        $app['config']->set('database.default', 'testbench');

        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }
}
