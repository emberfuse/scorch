<?php

namespace Emberfuse\Scorch;

use Emberfuse\Scorch\Support\Util;
use Emberfuse\Scorch\Tests\TestCase;
use Emberfuse\Scorch\Tests\Traits\HasUserAttributes;
use Emberfuse\Scorch\Tests\Fixtures\TestAuthenticationUser;

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
