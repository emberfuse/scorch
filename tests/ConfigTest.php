<?php

namespace Cratespace\Sentinel\Tests;

use Cratespace\Sentinel\Sentinel\Config;

class ConfigTest extends TestCase
{
    public function testGetsAllSentinelConfigs()
    {
        $this->assertEquals(config('sentinel'), $this->getConfigurations()->all());
    }

    public function testDynamicallyGetSpecificConfig()
    {
        config()->set('sentinel.foo', 'bar');

        $this->assertEquals('bar', Config::foo());
    }

    protected function getConfigurations(): Config
    {
        return new Config($this->app['config']);
    }
}
