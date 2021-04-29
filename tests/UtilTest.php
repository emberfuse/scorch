<?php

namespace Cratespace\Sentinel;

use Cratespace\Sentinel\Support\Util;
use Cratespace\Sentinel\Tests\TestCase;
use Cratespace\Sentinel\Tests\Traits\HasUserAttributes;
use Cratespace\Sentinel\Tests\Fixtures\TestAuthenticationUser;

class UtilTest extends TestCase
{
    use HasUserAttributes;

    public function testGetClassNameFromKeyWord()
    {
        $this->assertEquals('Normal', Util::className('normals'));
    }

    public function testMakeUsername()
    {
        $this->migrate();

        $this->assertEquals(
            'JamesSilverman',
            Util::makeUsername('James Silverman')
        );
    }

    public function testMakeUniqueUsername()
    {
        $this->migrate();

        $user = TestAuthenticationUser::forceCreate($this->userDetails());

        $this->assertNotEquals(
            $user->username,
            Util::makeUsername('James Silverman')
        );
    }
}
