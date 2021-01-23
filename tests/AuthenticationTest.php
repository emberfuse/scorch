<?php

namespace Citadel\Tests;

use Citadel\Citadel\Config;
use PragmaRX\Google2FA\Google2FA;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Citadel\Actions\AuthenticateUser;
use Citadel\Limiters\LoginRateLimiter;
use Illuminate\Support\Facades\Schema;
use Citadel\Contracts\Actions\AuthenticatesUsers;
use Citadel\Contracts\Responses\LoginViewResponse;
use Citadel\Tests\Fixtures\TestAuthenticationUser;
use Citadel\Tests\Fixtures\TestTwoFactorAuthenticationUser;

class AuthenticationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app->singleton(AuthenticatesUsers::class, AuthenticateUser::class);
    }

    public function testLoginViewResponseIsReturned()
    {
        $this->mock(LoginViewResponse::class)
            ->shouldReceive('toResponse')
            ->andReturn(response('login view'));

        $response = $this->withoutExceptionHandling()->get(route('login'));

        $response->assertStatus(200)->assertSeeText('login view');
    }

    public function testUserCanAuthenticate()
    {
        $this->loadLaravelMigrations(['--database' => 'testbench']);

        TestAuthenticationUser::forceCreate($this->userDetails());

        $response = $this->withoutExceptionHandling()->post('/login', [
            'email' => 'james.silverman@monster.com',
            'password' => 'cthuluEmployee',
        ]);

        $response->assertRedirect(Config::home());
    }

    public function testValidationExceptionReturnedOnFailure()
    {
        $this->loadLaravelMigrations(['--database' => 'testbench']);

        TestAuthenticationUser::forceCreate($this->userDetails());

        $response = $this->post('/login', [
            'email' => 'james.silverman@monster.com',
            'password' => 'cthuluHimself',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['email']);
    }

    public function testLoginAttemptsAreThrottled()
    {
        $this->mock(LoginRateLimiter::class, function ($mock) {
            $mock->shouldReceive('tooManyAttempts')->andReturn(true);
            $mock->shouldReceive('availableIn')->andReturn(10);
        });

        $response = $this->postJson('/login', [
            'email' => 'james.silverman@monster.com',
            'password' => 'cthuluEmployee',
        ]);

        $response->assertStatus(429);
        $response->assertJsonValidationErrors(['email']);
    }

    public function testTheUserCanLogoutOfTheApplication()
    {
        $this->loadLaravelMigrations(['--database' => 'testbench']);

        Auth::guard()->setUser(
            $user = TestAuthenticationUser::forceCreate($this->userDetails())
        );

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect('/');
        $this->assertNull(Auth::guard()->getUser());
    }

    public function testTheUserCanLogoutOfTheApplicationUsingJsonRequest()
    {
        $this->loadLaravelMigrations(['--database' => 'testbench']);

        Auth::guard()->setUser(
            $user = TestAuthenticationUser::forceCreate($this->userDetails())
        );

        $response = $this->actingAs($user)->postJson('/logout');

        $response->assertStatus(204);
        $this->assertNull(Auth::guard()->getUser());
    }

    public function testUserIsRedirectedToChallengeWhenUsingTwoFactorAuthentication()
    {
        app('config')->set('auth.providers.users.model', TestTwoFactorAuthenticationUser::class);

        $this->loadLaravelMigrations(['--database' => 'testbench']);

        Schema::table('users', function ($table) {
            $table->text('two_factor_secret')->nullable();
        });

        TestTwoFactorAuthenticationUser::forceCreate(
            $this->userDetails(['two_factor_secret' => 'test-secret'])
        );

        $response = $this->withoutExceptionHandling()->post('/login', [
            'email' => 'james.silverman@monster.com',
            'password' => 'cthuluEmployee',
        ]);

        $response->assertRedirect('/two-factor-challenge');
    }

    public function testTwoFactorChallengeCanBePassedViaCode()
    {
        app('config')->set(
            'auth.providers.users.model',
            TestTwoFactorAuthenticationUser::class
        );

        $this->loadLaravelMigrations(['--database' => 'testbench']);
        $this->artisan('migrate', ['--database' => 'testbench'])->run();

        $tfaEngine = app(Google2FA::class);
        $userSecret = $tfaEngine->generateSecretKey();
        $validOtp = $tfaEngine->getCurrentOtp($userSecret);

        $user = TestTwoFactorAuthenticationUser::forceCreate(
            $this->userDetails(['two_factor_secret' => encrypt($userSecret)])
        );

        $response = $this->withSession([
            'login.id' => $user->id,
            'login.remember' => false,
        ])->withoutExceptionHandling()->post('/two-factor-challenge', [
            'code' => $validOtp,
        ]);

        $response->assertRedirect(Config::home());
    }

    public function testTwoFactorChallengeCanBePassedViaRecoveryCode()
    {
        app('config')->set('auth.providers.users.model', TestTwoFactorAuthenticationUser::class);

        $this->loadLaravelMigrations(['--database' => 'testbench']);
        $this->artisan('migrate', ['--database' => 'testbench'])->run();

        $user = TestTwoFactorAuthenticationUser::forceCreate(
            $this->userDetails(['two_factor_recovery_codes' => encrypt(json_encode(['invalid-code', 'valid-code']))])
        );

        $response = $this->withSession([
            'login.id' => $user->id,
            'login.remember' => false,
        ])->withoutExceptionHandling()->post('/two-factor-challenge', [
            'recovery_code' => 'valid-code',
        ]);

        $response->assertRedirect(Config::home());
        $this->assertNotNull(Auth::getUser());
        $this->assertNotContains('valid-code', json_decode(decrypt($user->fresh()->two_factor_recovery_codes), true));
    }

    public function testTwoFactorChallengeCanFailViaRecoveryCode()
    {
        app('config')->set('auth.providers.users.model', TestTwoFactorAuthenticationUser::class);

        $this->loadLaravelMigrations(['--database' => 'testbench']);
        $this->artisan('migrate', ['--database' => 'testbench'])->run();

        $user = TestTwoFactorAuthenticationUser::forceCreate(
            $this->userDetails(['two_factor_recovery_codes' => encrypt(json_encode(['invalid-code', 'valid-code']))])
        );

        $response = $this->withSession([
            'login.id' => $user->id,
            'login.remember' => false,
        ])->withoutExceptionHandling()->post('/two-factor-challenge', [
            'recovery_code' => 'missing-code',
        ]);

        $response->assertRedirect('/login');
        $this->assertNull(Auth::getUser());
    }

    /**
     * Array of faker user details.
     *
     * @param array[] $overrides
     *
     * @return array
     */
    protected function userDetails(array $overrides = []): array
    {
        return array_merge([
            'name' => 'James Silverman',
            'email' => 'james.silverman@monster.com',
            'password' => Hash::make('cthuluEmployee'),
        ], $overrides);
    }
}
