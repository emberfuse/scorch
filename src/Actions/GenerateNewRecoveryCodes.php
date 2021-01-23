<?php

namespace Citadel\Actions;

use Citadel\Codes\RecoveryCode;
use Illuminate\Support\Collection;
use Citadel\Events\RecoveryCodesGenerated;
use Illuminate\Contracts\Auth\Authenticatable;

class GenerateNewRecoveryCodes
{
    /**
     * Generate new recovery codes for the user.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     *
     * @return void
     */
    public function __invoke(Authenticatable $user): void
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
