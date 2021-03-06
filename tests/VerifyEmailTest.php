<?php

namespace Emberfuse\Scorch\Tests;

use Mockery as m;
use Illuminate\Support\Facades\URL;
use Illuminate\Contracts\Auth\Authenticatable;

class VerifyEmailTest extends TestCase
{
    public function testTheEmailCanBeVerified()
    {
        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => 1,
                'hash' => sha1('taylor@laravel.com'),
            ]
        );

        $user = m::mock(Authenticatable::class);
        $user->shouldReceive('getKey')->andReturn(1);
        $user->shouldReceive('getAuthIdentifier')->andReturn(1);
        $user->shouldReceive('getEmailForVerification')->andReturn('taylor@laravel.com');
        $user->shouldReceive('hasVerifiedEmail')->andReturn(false);
        $user->shouldReceive('markEmailAsVerified')->once();

        $response = $this->actingAs($user)
            ->withSession(['url.intended' => 'http://foo.com/bar'])
            ->get($url);

        $response->assertRedirect('http://foo.com/bar');
    }

    public function testRedirectedIfEmailIsAlreadyVerified()
    {
        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => 1,
                'hash' => sha1('taylor@laravel.com'),
            ]
        );

        $user = m::mock(Authenticatable::class);
        $user->shouldReceive('getKey')->andReturn(1);
        $user->shouldReceive('getAuthIdentifier')->andReturn(1);
        $user->shouldReceive('getEmailForVerification')->andReturn('taylor@laravel.com');
        $user->shouldReceive('hasVerifiedEmail')->andReturn(true);
        $user->shouldReceive('markEmailAsVerified')->never();

        $response = $this->actingAs($user)->get($url);

        $response->assertStatus(302);
    }

    public function testEmailIsNotVerifiedIfIdDoesNotMatch()
    {
        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => 2,
                'hash' => sha1('taylor@laravel.com'),
            ]
        );

        $user = m::mock(Authenticatable::class);
        $user->shouldReceive('getKey')->andReturn(1);
        $user->shouldReceive('getAuthIdentifier')->andReturn(1);
        $user->shouldReceive('getEmailForVerification')->andReturn('taylor@laravel.com');

        $response = $this->actingAs($user)->get($url);

        $response->assertStatus(403);
    }

    public function testEmailIsNotVerifiedIfEmailDoesNotMatch()
    {
        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => 1,
                'hash' => sha1('abigail@laravel.com'),
            ]
        );

        $user = m::mock(Authenticatable::class);
        $user->shouldReceive('getKey')->andReturn(1);
        $user->shouldReceive('getAuthIdentifier')->andReturn(1);
        $user->shouldReceive('getEmailForVerification')->andReturn('taylor@laravel.com');

        $response = $this->actingAs($user)->get($url);

        $response->assertStatus(403);
    }
}
