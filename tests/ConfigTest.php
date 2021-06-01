<?php

namespace Emberfuse\Scorch\Tests;

use Emberfuse\Scorch\Scorch\Config;

class ConfigTest extends TestCase
{
    public function testGetsAllScorchConfigs()
    {
        $this->assertEquals(config('scorch'), $this->getConfigurations()->all());
    }

    public function testDynamicallyGetSpecificConfig()
    {
        config()->set('scorch.foo', 'bar');

        $this->assertEquals('bar', Config::foo());
    }

    protected function getConfigurations(): Config
    {
        return new Config($this->app['config']);
    }
}
