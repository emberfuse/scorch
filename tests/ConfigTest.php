<?php

namespace Citadel\Tests;

use Citadel\Citadel\Config;

class ConfigTest extends TestCase
{
    public function testGetsAllCitadelConfigs()
    {
        $this->assertEquals(config('citadel'), $this->getConfigurations()->all());
    }

    public function testDynamicallyGetSpecificConfig()
    {
        config()->set('citadel.foo', 'bar');

        $this->assertEquals('bar', Config::foo());
    }

    protected function getConfigurations(): Config
    {
        return new Config($this->app['config']);
    }
}
