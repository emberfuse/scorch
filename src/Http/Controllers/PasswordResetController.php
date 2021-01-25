<?php

namespace Cratespace\Citadel\Http\Controllers;

use Cratespace\Citadel\Citadel\Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Contracts\Support\Responsable;
use Symfony\Component\HttpFoundation\Response;
use Cratespace\Citadel\Http\Requests\PasswordResetRequest;
use Cratespace\Citadel\Http\Responses\PasswordResetResponse;
use Cratespace\Citadel\Contracts\Actions\ResetsUserPasswords;
use Cratespace\Citadel\Http\Responses\FailedPasswordResetResponse;
use Cratespace\Citadel\Contracts\Responses\ResetPasswordViewResponse;

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
     * @param \Illuminate\Http\Request                               $request
     * @param \Citadel\Contracts\Responses\ResetPasswordViewResponse $response
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function create(Request $request, ResetPasswordViewResponse $response): Response
    {
        return $response->toResponse($request);
    }

    /**
     * Reset the user's password.
     *
     * @param \Illuminate\Http\Request                       $request
     * @param \Citadel\Contracts\Actions\ResetsUserPasswords $reseter
     *
     * @return \Illuminate\Contracts\Support\Responsable
     */
    public function store(PasswordResetRequest $request, ResetsUserPasswords $reseter): Responsable
    {
        $status = $reseter->reset($request->only(
            Config::email(),
            'password',
            'password_confirmation',
            'token'
        ));

        return $status == Password::PASSWORD_RESET
            ? $this->app(PasswordResetResponse::class, ['status' => $status])
            : $this->app(FailedPasswordResetResponse::class, ['status' => $status]);
    }
}
