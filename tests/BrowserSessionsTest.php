<?php

namespace Cratespace\Sentinel\Tests;

use Cratespace\Sentinel\Tests\Traits\HasUserAttributes;
use Cratespace\Sentinel\Tests\Fixtures\TestAuthenticationUser;

class BrowserSessionsTest extends TestCase
{
    use HasUserAttributes;

    public function testOtherBrowserSessionsCanBeLoggedOut()
    {
        $this->migrate();

        TestAuthenticationUser::forceCreate($this->userDetails());

        $response = $this->delete('/user/other-browser-sessions', [
            'password' => 'cthuluEmployee',
        ]);

        $response->assertSessionHasNoErrors();
    }
}
