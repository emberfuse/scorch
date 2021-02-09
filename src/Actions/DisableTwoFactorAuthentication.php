<?php

namespace Cratespace\Sentinel\Actions;

use Illuminate\Foundation\Auth\User;
use Cratespace\Sentinel\Events\TwoFactorAuthenticationDisabled;

class DisableTwoFactorAuthentication
{
    /**
     * Disable two factor authentication for the user.
     *
     * @param \Illuminate\Foundation\Auth\User $user
     *
     * @return void
     */
    public function __invoke(User $user): void
    {
        $user->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
        ])->save();

        TwoFactorAuthenticationDisabled::dispatch($user);
    }
}
