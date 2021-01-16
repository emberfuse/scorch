<?php

namespace Citadel\Tests;

use Citadel\Auth;
use PHPUnit\Framework\TestCase;

class AuthTest extends TestCase
{
    public function testBasic()
    {
        $auth = new Auth('Example Auth');

        $this->assertInstanceOf(Auth::class, $auth);
        $this->assertEquals('Example Auth', $auth->name());
    }
}
