<?php

namespace Citadel\Tests;

use Mockery;
use Citadel\Providers\CitadelServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    protected function getPackageProviders($app)
    {
        return [CitadelServiceProvider::class];
    }
}
