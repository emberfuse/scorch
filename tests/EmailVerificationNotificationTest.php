<?php

namespace Cratespace\Citadel\Tests;

use Mockery as m;
use Illuminate\Contracts\Auth\Authenticatable;

class EmailVerificationNotificationTest extends TestCase
{
    public function testEmailVerificationNotificationCanBeSent()
    {
        $user = m::mock(Authenticatable::class);

        $user->shouldReceive('hasVerifiedEmail')->andReturn(false);
        $user->shouldReceive('getAuthIdentifier')->andReturn(1);
        $user->shouldReceive('sendEmailVerificationNotification')->once();

        $response = $this->from('/email/verify')
            ->actingAs($user)
            ->post('/email/verification-notification');

        $response->assertRedirect('/email/verify');
    }

    public function testUserIsRedirectIfAlreadyVerified()
    {
        $user = m::mock(Authenticatable::class);

        $user->shouldReceive('hasVerifiedEmail')->andReturn(true);
        $user->shouldReceive('getAuthIdentifier')->andReturn(1);
        $user->shouldReceive('sendEmailVerificationNotification')->never();

        $response = $this->from('/email/verify')
            ->actingAs($user)
            ->post('/email/verification-notification');

        $response->assertRedirect('/home');
    }

    public function testUserIsRedirectToIntendedUrlIfAlreadyVerified()
    {
        $user = m::mock(Authenticatable::class);

        $user->shouldReceive('hasVerifiedEmail')->andReturn(true);
        $user->shouldReceive('getAuthIdentifier')->andReturn(1);
        $user->shouldReceive('sendEmailVerificationNotification')->never();

        $response = $this->from('/email/verify')
            ->actingAs($user)
            ->withSession(['url.intended' => 'http://foo.com/bar'])
            ->post('/email/verification-notification');

        $response->assertRedirect('http://foo.com/bar');
    }
}
