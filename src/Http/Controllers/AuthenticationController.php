<?php

namespace Emberfuse\Scorch\Http\Controllers;

use Emberfuse\Scorch\Scorch\Config;
use Emberfuse\Scorch\Auth\DenyLockedAccount;
use Emberfuse\Scorch\Auth\AttemptToAuthenticate;
use Emberfuse\Scorch\Http\Requests\LoginRequest;
use Emberfuse\Scorch\Http\Requests\LogoutRequest;
use Emberfuse\Scorch\Http\Responses\LoginResponse;
use Emberfuse\Scorch\Http\Responses\LogoutResponse;
use Emberfuse\Scorch\Auth\EnsureLoginIsNotThrottled;
use Emberfuse\Scorch\Contracts\Actions\LogsoutUsers;
use Emberfuse\Scorch\Auth\PrepareAuthenticatedSession;
use Emberfuse\Scorch\Contracts\Responses\LoginViewResponse;
use Emberfuse\Scorch\Auth\RedirectIfTwoFactorAuthenticatable;

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
     * @param \Illuminate\Http\Request                               $request
     * @param \Emberfue\Scorch\Contracts\Responses\LoginViewResponse $response
     *
     * @return mixed
     */
    public function create()
    {
        return $this->resolve(LoginViewResponse::class);
    }

    /**
     * Attempt to authenticate a new session.
     *
     * @param \Emberfuse\Scorch\Http\Requests\LoginRequest $request
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
     * @param \Emberfuse\Scorch\Http\Requests\LogoutRequest $request
     * @param \Emberfuse\Scorch\Contracts\Auth\LogsoutUsers $logoutAction
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function destroy(LogoutRequest $request, LogsoutUsers $logoutAction)
    {
        $logoutAction->logout($request);

        return LogoutResponse::dispatch();
    }
}
