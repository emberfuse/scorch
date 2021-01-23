<?php

namespace Citadel\Http\Controllers;

use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Support\Responsable;
use Citadel\Http\Requests\TwoFactorLoginRequest;
use Citadel\Http\Responses\TwoFactorLoginResponse;
use Citadel\Http\Responses\FailedTwoFactorLoginResponse;

class TwoFactorAuthenticationController extends Controller
{
    /**
     * The guard implementation.
     *
     * @var \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected $guard;

    /**
     * Create a new controller instance.
     *
     * @param \Illuminate\Contracts\Auth\StatefulGuard $guard
     *
     * @return void
     */
    public function __construct(StatefulGuard $guard)
    {
        $this->guard = $guard;
    }

    /**
     * Show the two factor authentication challenge view.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\Support\Responsable
     */
    public function create(): Responsable
    {
        return app(TwoFactorChallengeViewResponse::class);
    }

    /**
     * Attempt to authenticate a new session using the two factor authentication code.
     *
     * @param \Citadel\Http\Requests\TwoFactorLoginRequest $request
     *
     * @return \Illuminate\Contracts\Support\Responsable
     */
    public function store(TwoFactorLoginRequest $request): Responsable
    {
        $user = $request->challengedUser();

        if ($code = $request->validRecoveryCode()) {
            $user->replaceRecoveryCode($code);
        } elseif (! $request->hasValidCode()) {
            return app(FailedTwoFactorLoginResponse::class);
        }

        $this->guard->login($user, $request->remember());

        $request->session()->regenerate();

        return app(TwoFactorLoginResponse::class);
    }
}
