<?php

namespace Citadel\Tests;

use Illuminate\Support\Facades\Event;
use Citadel\Events\RecoveryCodesGenerated;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Citadel\Tests\Fixtures\TestTwoFactorRecoveryCodeUser;

class RecoveryCodeGenerationTest extends TestCase
{
    use WithoutMiddleware;

    public function testNewRecoveryCodesCanBeGenerated()
    {
        Event::fake();

        $this->migrate();

        $user = TestTwoFactorRecoveryCodeUser::forceCreate([
            'name' => 'James Silverman',
            'username' => 'Silver Monster',
            'email' => 'silver.james@monster.com',
            'password' => bcrypt('cthuluEmployee'),
        ]);

        $response = $this->withoutExceptionHandling()->actingAs($user)->postJson(
            '/user/two-factor-recovery-codes'
        );

        $response->assertStatus(200);

        Event::assertDispatched(RecoveryCodesGenerated::class);

        $user->fresh();

        $this->assertNotNull($user->two_factor_recovery_codes);
        $this->assertIsArray(json_decode(decrypt($user->two_factor_recovery_codes), true));
    }
}