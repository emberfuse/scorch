<?php

namespace Cratespace\Sentinel\Http\Controllers;

use Cratespace\Sentinel\Sentinel\Config;
use Illuminate\Support\Facades\Password;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Contracts\Support\Responsable;
use Symfony\Component\HttpFoundation\Response;
use Cratespace\Sentinel\Http\Requests\PasswordResetRequest;
use Cratespace\Sentinel\Http\Responses\PasswordResetResponse;
use Cratespace\Sentinel\Contracts\Actions\ResetsUserPasswords;
use Cratespace\Sentinel\Http\Responses\FailedPasswordResetResponse;
use Cratespace\Sentinel\Contracts\Responses\ResetPasswordViewResponse;

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
     * @param \Sentinel\Contracts\Responses\ResetPasswordViewResponse $response
     *
     * @return \Illuminate\Contracts\Support\Responsable
     */
    public function create(): Responsable
    {
        return $this->resolve(ResetPasswordViewResponse::class);
    }

    /**
     * Reset the user's password.
     *
     * @param \Illuminate\Http\Request                        $request
     * @param \Sentinel\Contracts\Actions\ResetsUserPasswords $reseter
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function store(PasswordResetRequest $request, ResetsUserPasswords $reseter): Response
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
