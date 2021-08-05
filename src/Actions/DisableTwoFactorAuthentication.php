<?php

namespace Emberfuse\Scorch\Actions;

use Emberfuse\Scorch\Events\TwoFactorAuthenticationDisabled;
use Illuminate\Foundation\Auth\User;

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
