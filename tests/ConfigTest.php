
<?php

use Citadel\Auth\Config;
use Citadel\Tests\TestCase;
use Citadel\Providers\CitadelServiceProvider;

class ConfigTest extends TestCase
{
    public function test_gets_all_citadel_configs()
    {
        $this->assertEquals(config('citadel'), $this->getConfigurations()->all());
    }

    protected function getConfigurations(): Config
    {
        return (new Config($this->app['config']));
    }

    protected function getPackageProviders($app)
    {
        return [CitadelServiceProvider::class];
    }
}
