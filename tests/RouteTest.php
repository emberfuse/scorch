<?php

namespace Cratespace\Sentinel\Tests;

use Cratespace\Sentinel\Sentinel\Route;

class RouteTest extends TestCase
{
    public function testDetermineRouteIsEnabled()
    {
        config()->set('sentinel.auth_routes.login', false);

        $this->assertFalse(Route::isEnabled('login'));
    }

    public function testDetermineNonExistantRouteIsEnabled()
    {
        $this->assertFalse(Route::isEnabled('user-profile'));
    }
}
