<?php

namespace Cratespace\Sentinel\Tests;

use PHPUnit\Framework\TestCase;
use Illuminate\Database\Eloquent\Model;
use Cratespace\Sentinel\Auth\Tokens\TransientToken;
use Cratespace\Sentinel\Models\Traits\HasApiTokens;
use Cratespace\Sentinel\Auth\Tokens\PersonalAccessToken;

class HasApiTokensTest extends TestCase
{
    public function testTokensCanBeCreated()
    {
        $class = new ClassThatHasApiTokens();

        $newToken = $class->createToken('test', ['foo']);

        [$id, $token] = explode('|', $newToken->plainTextToken);

        $this->assertEquals(
            $newToken->accessToken->token,
            hash('sha256', $token)
        );

        $this->assertEquals(
            $newToken->accessToken->id,
            $id
        );
    }

    public function testCanCheckTokenAbilities()
    {
        $class = new ClassThatHasApiTokens();

        $class->withAccessToken(new TransientToken());

        $this->assertTrue($class->tokenCan('foo'));
    }
}

class ClassThatHasApiTokens extends Model
{
    use HasApiTokens;

    public function tokens()
    {
        return new class() {
            public function create(array $attributes)
            {
                return new PersonalAccessToken($attributes);
            }
        };
    }
}
