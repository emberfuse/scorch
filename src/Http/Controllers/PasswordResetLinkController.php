<?php

namespace Cratespace\Citadel\Http\Controllers;

use Illuminate\Http\Request;
use Cratespace\Citadel\Citadel\Config;
use Illuminate\Support\Facades\Password;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Contracts\Support\Responsable;
use Cratespace\Citadel\Http\Requests\PasswordResetLinkRequest;
use Cratespace\Citadel\Http\Responses\FailedPasswordResetLinkRequestResponse;
use Cratespace\Citadel\Contracts\Responses\RequestPasswordResetLinkViewResponse;
use Cratespace\Citadel\Http\Responses\SuccessfulPasswordResetLinkRequestResponse;

class PasswordResetLinkController extends Controller
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
     * Show the reset password link request view.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\Support\Responsable
     */
    public function create(): Responsable
    {
        return $this->app(RequestPasswordResetLinkViewResponse::class);
    }

    /**
     * Send a reset link to the given user.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\Support\Responsable
     */
    public function store(PasswordResetLinkRequest $request): Responsable
    {
        $status = $this->broker->sendResetLink($request->only(Config::email()));

        return $status == Password::RESET_LINK_SENT
            ? $this->app(SuccessfulPasswordResetLinkRequestResponse::class, ['status' => $status])
            : $this->app(FailedPasswordResetLinkRequestResponse::class, ['status' => $status]);
    }
}
