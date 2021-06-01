<?php

namespace Emberfuse\Scorch\Tests;

use Emberfuse\Scorch\Scorch\Route;

class RouteTest extends TestCase
{
    public function testDetermineRouteIsEnabled()
    {
        config()->set('scorch.auth_routes.login', false);

        $this->assertFalse(Route::isEnabled('login'));
    }

    public function testDetermineNonExistantRouteIsEnabled()
    {
        $this->assertFalse(Route::isEnabled('user-profile'));
    }
}
