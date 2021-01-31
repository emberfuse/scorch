<?php

namespace Cratespace\Citadel\Tests;

use Cratespace\Citadel\Tests\Traits\HasUserAttributes;
use Cratespace\Citadel\Tests\Fixtures\TestAuthenticationUser;

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
