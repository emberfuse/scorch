<?php

namespace Cratespace\Citadel\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\StatefulGuard;
use Symfony\Component\HttpFoundation\Response;
use Cratespace\Citadel\Contracts\Actions\ConfirmsPasswords;
use Cratespace\Citadel\Http\Requests\ConfirmPasswordRequest;
use Cratespace\Citadel\Http\Responses\PasswordConfirmedResponse;
use Cratespace\Citadel\Contracts\Responses\ConfirmPasswordViewResponse;
use Cratespace\Citadel\Http\Responses\FailedPasswordConfirmationResponse;

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
     * @param \Illuminate\Http\Request                                 $request
     * @param \Citadel\Contracts\Responses\ConfirmPasswordViewResponse $response
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function show(Request $request, ConfirmPasswordViewResponse $response): Response
    {
        return $response->toResponse($request);
    }

    /**
     * Confirm the user's password.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\Support\Responsable
     */
    public function store(ConfirmPasswordRequest $request, ConfirmsPasswords $confirmer)
    {
        $confirmed = $confirmer->confirm(
            $this->guard,
            $request->user(),
            $request->input('password')
        );

        return $confirmed
            ? $this->app(PasswordConfirmedResponse::class)
            : $this->app(FailedPasswordConfirmationResponse::class);
    }
}
