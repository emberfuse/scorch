<?php

namespace Cratespace\Citadel\Tests;

use Mockery as m;
use Illuminate\Contracts\Auth\Authenticatable;
use Cratespace\Citadel\Contracts\Responses\VerifyEmailViewResponse;

class EmailVerificationPromptTest extends TestCase
{
    public function testTheEmailVerificationPromptViewIsReturned()
    {
        $this->withoutExceptionHandling();

        $this->mock(VerifyEmailViewResponse::class)
            ->shouldReceive('toResponse')
            ->andReturn(response('hello world'));

        $user = m::mock(Authenticatable::class);
        $user->shouldReceive('hasVerifiedEmail')->andReturn(false);

        $response = $this->actingAs($user)->get('/email/verify');

        $response->assertStatus(200);
        $response->assertSeeText('hello world');
    }

    public function testUserIsRedirectHomeIfAlreadyVerified()
    {
        $this->mock(VerifyEmailViewResponse::class)
            ->shouldReceive('toResponse')
            ->andReturn(response('hello world'));

        $user = m::mock(Authenticatable::class);
        $user->shouldReceive('hasVerifiedEmail')->andReturn(true);

        $response = $this->actingAs($user)->get('/email/verify');

        $response->assertRedirect('/home');
    }

    public function testUserIsRedirectToIntendedUrlIfAlreadyVerified()
    {
        $this->mock(VerifyEmailViewResponse::class)
            ->shouldReceive('toResponse')
            ->andReturn(response('hello world'));

        $user = m::mock(Authenticatable::class);
        $user->shouldReceive('hasVerifiedEmail')->andReturn(true);

        $response = $this->actingAs($user)
            ->withSession(['url.intended' => 'http://foo.com/bar'])
            ->get('/email/verify');

        $response->assertRedirect('http://foo.com/bar');
    }
}
