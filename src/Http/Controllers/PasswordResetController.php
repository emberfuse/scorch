<?php

namespace Emberfuse\Scorch\Http\Controllers;

use Emberfuse\Scorch\Scorch\Config;
use Illuminate\Support\Facades\Password;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Contracts\Support\Responsable;
use Emberfuse\Scorch\Http\Requests\PasswordResetRequest;
use Emberfuse\Scorch\Http\Responses\PasswordResetResponse;
use Emberfuse\Scorch\Contracts\Actions\ResetsUserPasswords;
use Emberfuse\Scorch\Http\Responses\FailedPasswordResetResponse;
use Emberfuse\Scorch\Contracts\Responses\ResetPasswordViewResponse;

class PasswordResetController extends Controller
{
    /**
     * Instance of the password broker implementation.
     *
     * @var \Illuminate\Contracts\Auth\PasswordBroker
     */
    protected $broker;

    /**
     * Create a new controller instance.
     *
     * @param \Illuminate\Contracts\Auth\PasswordBroker $broker
     *
     * @return void
     */
    public function __construct(PasswordBroker $broker)
    {
        $this->broker = $broker;
    }

    /**
     * Show the new password view.
     *
     * @param \Illuminate\Http\Request                                $request
     * @param \Emberfuse\Scorch\Contracts\Responses\ResetPasswordViewResponse $response
     *
     * @return \Illuminate\Contracts\Support\Responsable
     */
    public function create()
    {
        return $this->resolve(ResetPasswordViewResponse::class);
    }

    /**
     * Reset the user's password.
     *
     * @param \Illuminate\Http\Request                        $request
     * @param \Emberfuse\Scorch\Contracts\Actions\ResetsUserPasswords $reseter
     *
     * @return mixed
     */
    public function store(PasswordResetRequest $request, ResetsUserPasswords $reseter)
    {
        $status = $reseter->reset($request->only(
            Config::email(),
            'password',
            'password_confirmation',
            'token'
        ));

        return $status == Password::PASSWORD_RESET
            ? PasswordResetResponse::dispatch($status)
            : FailedPasswordResetResponse::dispatch($status);
    }
}
