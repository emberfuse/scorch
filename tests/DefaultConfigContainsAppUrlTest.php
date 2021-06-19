<?php

namespace Emberfuse\Scorch;

use Illuminate\Http\Request;
use Orchestra\Testbench\TestCase;
use Emberfuse\Scorch\Http\Middleware\EnsureFrontendRequestsAreStateful;

class DefaultConfigContainsAppUrlTest extends TestCase
{
    protected function getEnvironmentSetUp($app)
    {
        putenv('APP_URL=https://www.example.com');
        $config = require __DIR__ . '/../config/scorch.php';

        $app['config']->set('scorch.stateful', $config['stateful']);
    }

    public function testDefaultConfigContainsAppUrl()
    {
        $config = require __DIR__ . '/../config/scorch.php';

        $app_host = parse_url(env('APP_URL'), \PHP_URL_HOST);

        $this->assertContains($app_host, $config['stateful']);
    }

    public function testAppUrlIsNotParsedWhenMissingFromEnv()
    {
        putenv('APP_URL');

        $config = require __DIR__ . '/../config/scorch.php';

        $this->assertNull(env('APP_URL'));
        $this->assertNotContains('', $config['stateful']);
    }

    public function testRequestFromAppUrlIsStatefulWithDefaultConfig()
    {
        $request = Request::create('/');

        $request->headers->set('referer', env('APP_URL'));

        $this->assertTrue(EnsureFrontendRequestsAreStateful::fromFrontend($request));
    }
}
