<?php

namespace Cratespace\Sentinel\Http\Controllers;

use Cratespace\Sentinel\Sentinel\Config;
use Illuminate\Contracts\Support\Responsable;
use Cratespace\Sentinel\Auth\DenyLockedAccount;
use Cratespace\Sentinel\Auth\AttemptToAuthenticate;
use Cratespace\Sentinel\Http\Requests\LoginRequest;
use Cratespace\Sentinel\Http\Requests\LogoutRequest;
use Cratespace\Sentinel\Http\Responses\LoginResponse;
use Cratespace\Sentinel\Http\Responses\LogoutResponse;
use Cratespace\Sentinel\Auth\EnsureLoginIsNotThrottled;
use Cratespace\Sentinel\Contracts\Actions\LogsoutUsers;
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
        DenyLockedAccount::class,
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
        return $this->resolve(LoginViewResponse::class);
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
            ->through(array_filter(static::loginPipeline()))
            ->then(fn () => LoginResponse::dispatch());
    }

    /**
     * Get array of authentication middlware.
     *
     * @return array
     */
    public static function loginPipeline(): array
    {
        return array_filter(array_merge(
            static::$loginPipeline,
            Config::loginPipeline()
        ));
    }

    /**
     * Destroy an authenticated session.
     *
     * @param \Cratespace\Sentinel\Http\Requests\LogoutRequest $request
     * @param \Cratespace\Sentinel\Contracts\Auth\LogsoutUsers $logoutAction
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function destroy(LogoutRequest $request, LogsoutUsers $logoutAction)
    {
        $logoutAction->logout($request);

        return LogoutResponse::dispatch();
    }
}
