<?php

namespace Emberfuse\Scorch\Tests;

use Emberfuse\Scorch\Tests\Traits\HasUserAttributes;
use Emberfuse\Scorch\Tests\Fixtures\TestAuthenticationUser;

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
