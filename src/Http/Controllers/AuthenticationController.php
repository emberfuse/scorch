<?php

namespace Citadel\Http\Controllers;

use Illuminate\Session\Store;
use Citadel\Auth\AttemptToAuthenticate;
use Citadel\Http\Requests\LoginRequest;
use Citadel\Http\Requests\LogoutRequest;
use Citadel\Http\Responses\LoginResponse;
use Citadel\Http\Responses\LogoutResponse;
use Citadel\Auth\EnsureLoginIsNotThrottled;
use Illuminate\Contracts\Auth\StatefulGuard;
use Citadel\Auth\PrepareAuthenticatedSession;
use Illuminate\Contracts\Support\Responsable;
use Citadel\Contracts\Responses\LoginViewResponse;
use Citadel\Auth\RedirectIfTwoFactorAuthenticatable;

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
