<?php

namespace Emberfuse\Scorch\Http\Controllers;

use Emberfuse\Scorch\Contracts\Responses\TwoFactorChallengeViewResponse;
use Emberfuse\Scorch\Events\RecoveryCodeReplaced;
use Emberfuse\Scorch\Http\Requests\TwoFactorLoginRequest;
use Emberfuse\Scorch\Http\Responses\FailedTwoFactorLoginResponse;
use Emberfuse\Scorch\Http\Responses\TwoFactorLoginResponse;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Exceptions\HttpResponseException;

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
     * @param \Emberfuse\Scorch\Http\Requests\TwoFactorLoginRequest $request
     *
     * @return \Illuminate\Contracts\Support\Responsable
     */
    public function create(TwoFactorLoginRequest $request)
    {
        if (! $request->hasChallengedUser()) {
            throw new HttpResponseException(redirect()->route('login'));
        }

        return $this->resolve(TwoFactorChallengeViewResponse::class);
    }

    /**
     * Attempt to authenticate a new session using the two factor authentication code.
     *
     * @param \Emberfuse\Scorch\Http\Requests\TwoFactorLoginRequest $request
     *
     * @return mixed
     */
    public function store(TwoFactorLoginRequest $request)
    {
        $user = $request->challengedUser();

        if ($code = $request->validRecoveryCode()) {
            $user->replaceRecoveryCode($code);

            event(new RecoveryCodeReplaced($user, $code));
        } elseif (! $request->hasValidCode()) {
            return FailedTwoFactorLoginResponse::dispatch();
        }

        $this->guard->login($user, $request->remember());

        $request->session()->regenerate();

        return TwoFactorLoginResponse::dispatch();
    }
}
