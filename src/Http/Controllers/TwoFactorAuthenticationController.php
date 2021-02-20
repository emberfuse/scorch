<?php

namespace Cratespace\Sentinel\Http\Controllers;

use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Support\Responsable;
use Cratespace\Sentinel\Http\Requests\TwoFactorLoginRequest;
use Cratespace\Sentinel\Http\Responses\TwoFactorLoginResponse;
use Cratespace\Sentinel\Http\Responses\FailedTwoFactorLoginResponse;
use Cratespace\Sentinel\Contracts\Responses\TwoFactorChallengeViewResponse;

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
        return $this->app(TwoFactorChallengeViewResponse::class);
    }

    /**
     * Attempt to authenticate a new session using the two factor authentication code.
     *
     * @param \Sentinel\Http\Requests\TwoFactorLoginRequest $request
     *
     * @return mixed
     */
    public function store(TwoFactorLoginRequest $request)
    {
        $user = $request->challengedUser();

        if ($code = $request->validRecoveryCode()) {
            $user->replaceRecoveryCode($code);
        } elseif (! $request->hasValidCode()) {
            return FailedTwoFactorLoginResponse::dispatch();
        }

        $this->guard->login($user, $request->remember());

        $request->session()->regenerate();

        return TwoFactorLoginResponse::dispatch();
    }
}
