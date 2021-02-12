<?php

namespace Cratespace\Sentinel\Http\Controllers;

use Illuminate\Session\Store;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Support\Responsable;
use Symfony\Component\HttpFoundation\Response;
use Cratespace\Sentinel\Auth\AttemptToAuthenticate;
use Cratespace\Sentinel\Http\Requests\LoginRequest;
use Cratespace\Sentinel\Http\Requests\LogoutRequest;
use Cratespace\Sentinel\Http\Responses\LoginResponse;
use Cratespace\Sentinel\Http\Responses\LogoutResponse;
use Cratespace\Sentinel\Auth\EnsureLoginIsNotThrottled;
use Cratespace\Sentinel\Auth\PrepareAuthenticatedSession;
use Cratespace\Sentinel\Contracts\Responses\LoginViewResponse;
use Cratespace\Sentinel\Auth\RedirectIfTwoFactorAuthenticatable;

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
     * @param \Illuminate\Http\Request                        $request
     * @param \Sentinel\Contracts\Responses\LoginViewResponse $response
     *
     * @return mixed
     */
    public function create(): Responsable
    {
        return $this->app(LoginViewResponse::class);
    }

    /**
     * Attempt to authenticate a new session.
     *
     * @param \Sentinel\Http\Requests\LoginRequest $request
     *
     * @return mixed
     */
    public function store(LoginRequest $request)
    {
        return $this->pipeline()
            ->send($request)
            ->through(array_filter(static::$loginPipeline))
            ->then(fn ($request) => LoginResponse::dispatch());
    }

    /**
     * Destroy an authenticated session.
     *
     * @param \Sentinel\Http\Requests\LogoutRequest    $request
     * @param \Illuminate\Contracts\Auth\StatefulGuard $guard
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function destroy(LogoutRequest $request, StatefulGuard $guard): Response
    {
        $guard->logout();

        tap($request->session(), function ($session): void {
            $session->invalidate();

            $session->regenerateToken();
        });

        return LogoutResponse::dispatch();
    }
}
