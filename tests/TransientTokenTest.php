<?php

namespace Emberfuse\Scorch\Tests;

use PHPUnit\Framework\TestCase;
use Emberfuse\Scorch\API\Tokens\TransientToken;

class TransientTokenTest extends TestCase
{
    public function testCanDetermineWhatItCanAndCantDo()
    {
        $token = new TransientToken();

        $this->assertTrue($token->can('foo'));
        $this->assertTrue($token->can('bar'));
        $this->assertFalse($token->cant('foo'));
        $this->assertFalse($token->cant('bar'));
    }
}
