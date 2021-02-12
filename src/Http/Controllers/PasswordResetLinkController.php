<?php

namespace Cratespace\Sentinel\Http\Controllers;

use Illuminate\Http\Request;
use Cratespace\Sentinel\Sentinel\Config;
use Illuminate\Support\Facades\Password;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Contracts\Support\Responsable;
use Cratespace\Sentinel\Http\Requests\PasswordResetLinkRequest;
use Cratespace\Sentinel\Http\Responses\FailedPasswordResetLinkRequestResponse;
use Cratespace\Sentinel\Contracts\Responses\RequestPasswordResetLinkViewResponse;
use Cratespace\Sentinel\Http\Responses\SuccessfulPasswordResetLinkRequestResponse;
use Symfony\Component\HttpFoundation\Response;

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
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function store(PasswordResetLinkRequest $request): Response
    {
        $status = $this->broker->sendResetLink($request->only(Config::email()));

        return $status == Password::RESET_LINK_SENT
            ? SuccessfulPasswordResetLinkRequestResponse::dispatch($status)
            : FailedPasswordResetLinkRequestResponse::dispatch($status);
    }
}
