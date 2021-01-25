<?php

namespace Cratespace\Citadel\Tests;

use Cratespace\Citadel\Citadel\View;

class CitadelServiceProviderTest extends TestCase
{
    public function testViewsCanBeCustomized()
    {
        View::login(fn () => 'foo');

        $response = $this->get('/login');

        $response->assertOk();
        $this->assertSame('foo', $response->content());
    }
}
