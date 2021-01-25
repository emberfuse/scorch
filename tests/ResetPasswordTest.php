<?php

namespace Cratespace\Citadel\Tests;

use Mockery as m;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Password;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Contracts\Auth\Authenticatable;
use Cratespace\Citadel\Contracts\Actions\ResetsUserPasswords;
use Cratespace\Citadel\Contracts\Responses\ResetPasswordViewResponse;

class ResetPasswordTest extends TestCase
{
    public function testTheNewPasswordViewIsReturned()
    {
        $this->mock(ResetPasswordViewResponse::class)
            ->shouldReceive('toResponse')
            ->andReturn(response('hello world'));

        $response = $this->get('/reset-password/token');

        $response->assertStatus(200);
        $response->assertSeeText('hello world');
    }

    public function testPasswordCanBeReset()
    {
        Password::shouldReceive('broker')->andReturn(
            $broker = m::mock(PasswordBroker::class)
        );

        $guard = $this->mock(StatefulGuard::class);
        $user = m::mock(Authenticatable::class);

        $guard->shouldReceive('login')->never();

        $updater = $this->mock(ResetsUserPasswords::class);
        $updater->shouldReceive('reset')
            ->once()
            ->with(m::type('array'))
            ->andReturn(Password::PASSWORD_RESET);

        $broker->shouldReceive('reset')->andReturnUsing(function ($input, $callback) use ($user) {
            $callback($user, 'password');

            return Password::PASSWORD_RESET;
        });

        $response = $this->withoutExceptionHandling()->post('/reset-password', $this->validParameters());

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function testPasswordResetCanFail()
    {
        Password::shouldReceive('broker')->andReturn(
            $broker = m::mock(PasswordBroker::class)
        );

        $guard = $this->mock(StatefulGuard::class);
        $user = m::mock(Authenticatable::class);

        $updater = $this->mock(ResetsUserPasswords::class);
        $updater->shouldReceive('reset')
            ->once()
            ->with(m::type('array'))
            ->andReturn(Password::INVALID_TOKEN);

        $broker->shouldReceive('reset')->andReturnUsing(function ($input, $callback) {
            return Password::INVALID_TOKEN;
        });

        $response = $this->withoutExceptionHandling()->post('/reset-password', $this->validParameters());

        $response->assertStatus(302);
        $response->assertSessionHasErrors('email');
    }

    public function testPasswordResetCanFailWithJson()
    {
        Password::shouldReceive('broker')->andReturn(
            $broker = m::mock(PasswordBroker::class)
        );

        $updater = $this->mock(ResetsUserPasswords::class);
        $updater->shouldReceive('reset')
            ->once()
            ->with(m::type('array'))
            ->andReturn(Password::INVALID_TOKEN);

        $broker->shouldReceive('reset')->andReturnUsing(function ($input, $callback) {
            return Password::INVALID_TOKEN;
        });

        $response = $this->postJson('/reset-password', $this->validParameters());

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
    }

    public function testPasswordCanBeResetWithCustomizedEmailAddressField()
    {
        Config::set('citadel.email', 'emailAddress');
        Password::shouldReceive('broker')->andReturn(
            $broker = m::mock(PasswordBroker::class)
        );

        $guard = $this->mock(StatefulGuard::class);
        $user = m::mock(Authenticatable::class);

        $guard->shouldReceive('login')->never();

        $updater = $this->mock(ResetsUserPasswords::class);
        $updater->shouldReceive('reset')
            ->once()
            ->with(m::type('array'))
            ->andReturn(Password::PASSWORD_RESET);

        $broker->shouldReceive('reset')->andReturnUsing(function ($input, $callback) use ($user) {
            $callback($user, 'password');

            return Password::PASSWORD_RESET;
        });

        $response = $this->withoutExceptionHandling()->post('/reset-password', $this->validParameters([
            'emailAddress' => 'silver.james@monster.com',
        ]));

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /**
     * get array of valid parameters for request.
     *
     * @param array $overrides
     *
     * @return array
     */
    protected function validParameters(array $overrides = []): array
    {
        return array_merge([
            'token' => 'token',
            'email' => 'silver.james@monster.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ], $overrides);
    }
}
