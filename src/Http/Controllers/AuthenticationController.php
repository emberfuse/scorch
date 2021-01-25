<?php

namespace Cratespace\Citadel\Http\Controllers;

use Illuminate\Session\Store;
use Cratespace\Citadel\Auth\AttemptToAuthenticate;
use Cratespace\Citadel\Http\Requests\LoginRequest;
use Cratespace\Citadel\Http\Requests\LogoutRequest;
use Cratespace\Citadel\Http\Responses\LoginResponse;
use Cratespace\Citadel\Http\Responses\LogoutResponse;
use Cratespace\Citadel\Auth\EnsureLoginIsNotThrottled;
use Illuminate\Contracts\Auth\StatefulGuard;
use Cratespace\Citadel\Auth\PrepareAuthenticatedSession;
use Illuminate\Contracts\Support\Responsable;
use Cratespace\Citadel\Contracts\Responses\LoginViewResponse;
use Cratespace\Citadel\Auth\RedirectIfTwoFactorAuthenticatable;

class AuthenticationController extends Controller
{
    /**
     * The list of classes (pipes) to be used for the authentication pipeline.
     *
     * @var array
     */
    protected static $loginPipeline = [
        EnsureLoginIsNotThrottled::class,
        RedirectIfTwoFactorAuthenticatable::class,
        AttemptToAuthenticate::class,
        PrepareAuthenticatedSession::class,
    ];

    /**
     * Show the login view.
     *
     * @param \Illuminate\Http\Request                       $request
     * @param \Citadel\Contracts\Responses\LoginViewResponse $response
     *
     * @return \Illuminate\Contracts\Support\Responsable
     */
    public function create(): Responsable
    {
        return $this->app(LoginViewResponse::class);
    }

    /**
     * Attempt to authenticate a new session.
     *
     * @param \Citadel\Http\Requests\LoginRequest $request
     *
     * @return \Illuminate\Contracts\Support\Responsable
     */
    public function store(LoginRequest $request): Responsable
    {
        return $this->pipeline()
            ->send($request)
            ->through(array_filter(static::$loginPipeline))
            ->then(fn ($request) => $this->app(LoginResponse::class));
    }

    /**
     * Destroy an authenticated session.
     *
     * @param \Citadel\Http\Requests\LogoutRequest     $request
     * @param \Illuminate\Contracts\Auth\StatefulGuard $guard
     *
     * @return \Illuminate\Contracts\Support\Responsable
     */
    public function destroy(LogoutRequest $request, StatefulGuard $guard): Responsable
    {
        $guard->logout();

        tap($request->session(), function (Store $session): void {
            $session->invalidate();

            $session->regenerateToken();
        });

        return $this->app(LogoutResponse::class);
    }
}
