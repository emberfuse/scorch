<?php

namespace Cratespace\Sentinel\Actions;

use Illuminate\Support\Collection;
use Illuminate\Foundation\Auth\User;
use Cratespace\Sentinel\Support\RecoveryCode;
use Cratespace\Sentinel\Events\RecoveryCodesGenerated;

class GenerateNewRecoveryCodes
{
    /**
     * Generate new recovery codes for the user.
     *
     * @param \Illuminate\Foundation\Auth\User $user
     *
     * @return void
     */
    public function __invoke(User $user): void
    {
        $user->forceFill(['two_factor_recovery_codes' => $this->generateCode()])->save();

        RecoveryCodesGenerated::dispatch($user);
    }

    /**
     * Generate recovery codes for user.
     *
     * @return string
     */
    protected function generateCode(): string
    {
        return encrypt(json_encode(
            Collection::times(8, fn () => RecoveryCode::generate())->all()
        ));
    }
}
