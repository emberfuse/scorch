<?php

namespace Cratespace\Citadel\Tests;

use Illuminate\Support\Facades\Event;
use Cratespace\Citadel\Tests\Traits\HasUserAttributes;
use Cratespace\Citadel\Events\TwoFactorAuthenticationEnabled;
use Cratespace\Citadel\Events\TwoFactorAuthenticationDisabled;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Cratespace\Citadel\Tests\Fixtures\TestTwoFactorAuthenticationUser;

class TwoFactorAuthenticationTest extends TestCase
{
    use HasUserAttributes;
    use WithoutMiddleware;

    public function testTwoFactorAuthenticationCanBeEnabled()
    {
        Event::fake();

        $this->migrate();

        $user = TestTwoFactorAuthenticationUser::forceCreate($this->userDetails());

        $response = $this->withoutExceptionHandling()->actingAs($user)->postJson(
            '/user/two-factor-authentication'
        );

        $response->assertStatus(200);

        Event::assertDispatched(TwoFactorAuthenticationEnabled::class);

        $user->fresh();

        $this->assertNotNull($user->two_factor_secret);
        $this->assertNotNull($user->two_factor_recovery_codes);
        $this->assertIsArray(json_decode(decrypt($user->two_factor_recovery_codes), true));
        $this->assertNotNull($user->twoFactorQrCodeSvg());
    }

    public function testTwoFactorAuthenticationCanBeDisabled()
    {
        Event::fake();

        $this->migrate();

        $user = TestTwoFactorAuthenticationUser::forceCreate($this->userDetails([
            'two_factor_secret' => encrypt('foo'),
            'two_factor_recovery_codes' => encrypt(json_encode([])),
        ]));

        $response = $this->withoutExceptionHandling()->actingAs($user)->deleteJson(
            '/user/two-factor-authentication'
        );

        $response->assertStatus(200);

        Event::assertDispatched(TwoFactorAuthenticationDisabled::class);

        $user->fresh();

        $this->assertNull($user->two_factor_secret);
        $this->assertNull($user->two_factor_recovery_codes);
    }
}
