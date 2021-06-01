<?php

namespace Emberfuse\Scorch\Tests;

use Mockery as m;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Emberfuse\Scorch\Providers\ScorchServiceProvider;
use Emberfuse\Scorch\Tests\Fixtures\TestAuthenticationUser;

abstract class TestCase extends BaseTestCase
{
    public function tearDown(): void
    {
        parent::tearDown();

        m::close();
    }

    /**
     * Get package providers.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [ScorchServiceProvider::class];
    }

    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['migrator']->path(__DIR__ . '/../database/migrations');

        $app['config']->set('scorch.stateful', ['test.com', '*.test.com']);

        $app['config']->set('auth.providers.users.model', TestAuthenticationUser::class);

        $app['config']->set('database.default', 'testbench');

        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    /**
     * Load migrations and create database.
     *
     * @return void
     */
    protected function migrate(): void
    {
        $this->loadLaravelMigrations(['--database' => 'testbench']);

        $this->artisan('migrate:fresh', ['--database' => 'testbench'])->run();
    }
}
