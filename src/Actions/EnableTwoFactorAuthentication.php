<?php

namespace Citadel\Actions;

use Citadel\Auth\RecoveryCode;
use Illuminate\Support\Collection;
use Illuminate\Foundation\Auth\User;
use Citadel\Events\TwoFactorAuthenticationEnabled;
use Citadel\Contracts\Providers\TwoFactorAuthenticationProvider;

class EnableTwoFactorAuthentication
{
    /**
     * The two factor authentication provider.
     *
     * @var \Citadel\Contracts\Providers\TwoFactorAuthenticationProvider
     */
    protected $provider;

    /**
     * Create a new action instance.
     *
     * @param \Citadel\Contracts\Providers\TwoFactorAuthenticationProvider $provider
     *
     * @return void
     */
    public function __construct(TwoFactorAuthenticationProvider $provider)
    {
        $this->provider = $provider;
    }

    /**
     * Enable two factor authentication for the user.
     *
     * @param \Illuminate\Foundation\Auth\User $user
     *
     * @return void
     */
    public function __invoke(User $user)
    {
        $user->forceFill([
            'two_factor_secret' => encrypt($this->provider->generateSecretKey()),
            'two_factor_recovery_codes' => encrypt(json_encode(Collection::times(8, function () {
                return RecoveryCode::generate();
            })->all())),
        ])->save();

        TwoFactorAuthenticationEnabled::dispatch($user);
    }
}
