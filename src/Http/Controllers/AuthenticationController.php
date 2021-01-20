<?php

namespace Citadel\Http\Controllers;

use Citadel\Auth\Config;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Routing\Controller;
use Citadel\Http\Requests\LoginRequest;
use Citadel\Contracts\AuthenticatesUsers;
use Citadel\Http\Responses\LoginResponse;
use Citadel\Http\Responses\LogoutResponse;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Contracts\Pipeline\Pipeline as PipelineContract;

class AuthenticationController extends Controller
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
     * Attempt to authenticate a new session.
     *
     * @param \Citadel\Http\Requests\LoginRequest   $request
     * @param \Citadel\Contracts\AuthenticatesUsers $authenticator
     *
     * @return \Illuminate\Contracts\Support\Responsable
     */
    public function store(LoginRequest $request, AuthenticatesUsers $authenticator): Responsable
    {
        return $this->sendThroughLoginPipeline($request)
            ->then(function ($request) use ($authenticator) {
                $authenticator->authenticate($request->validated());

                return app(LoginResponse::class);
            });
    }

    /**
     * Get the authentication pipeline instance.
     *
     * @param \Citadel\Http\Requests\LoginRequest $request
     *
     * @return \Illuminate\Contracts\Pipeline\Pipeline
     */
    protected function sendThroughLoginPipeline(LoginRequest $request): PipelineContract
    {
        return (new Pipeline(app()))->send($request)->through(
            array_filter(Config::loginPipeline())
        );
    }

    /**
     * Destroy an authenticated session.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Citadel\Http\Responses\LogoutResponse
     */
    public function destroy(Request $request): LogoutResponse
    {
        $this->guard->logout();

        tap($request, function (Request $request): void {
            $request->session()->invalidate();

            $request->session()->regenerateToken();
        });

        return app(LogoutResponse::class);
    }
}
