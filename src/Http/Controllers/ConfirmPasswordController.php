<?php

namespace Emberfuse\Scorch\Http\Controllers;

use Illuminate\Contracts\Auth\StatefulGuard;
use Symfony\Component\HttpFoundation\Response;
use Emberfuse\Scorch\Contracts\Actions\ConfirmsPasswords;
use Emberfuse\Scorch\Http\Requests\ConfirmPasswordRequest;
use Emberfuse\Scorch\Http\Responses\PasswordConfirmedResponse;
use Emberfuse\Scorch\Contracts\Responses\ConfirmPasswordViewResponse;
use Emberfuse\Scorch\Http\Responses\FailedPasswordConfirmationResponse;

class ConfirmPasswordController extends Controller
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
     * Show the confirm password view.
     *
     * @param \Illuminate\Http\Request                                $request
     * @param \Emberfuse\Scorch\Contracts\Responses\ConfirmPasswordViewResponse $response
     *
     * @return \Illuminate\Contracts\Support\Responsable
     */
    public function show()
    {
        return $this->resolve(ConfirmPasswordViewResponse::class);
    }

    /**
     * Confirm the user's password.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function store(ConfirmPasswordRequest $request, ConfirmsPasswords $confirmer): Response
    {
        $confirmed = $confirmer->confirm(
            $this->guard,
            $request->user(),
            $request->input('password')
        );

        return $confirmed
            ? PasswordConfirmedResponse::dispatch()
            : FailedPasswordConfirmationResponse::dispatch();
    }
}
