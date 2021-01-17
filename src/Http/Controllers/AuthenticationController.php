<?php

namespace Citadel\Http\Controllers;

use Citadel\Auth\Config;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Routing\Controller;
use Citadel\Http\Requests\LoginRequest;
use App\Providers\CitadelServiceProvider;
use Citadel\Contracts\AuthenticatesUsers;
use Citadel\Http\Responses\LoginResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Contracts\Pipeline\Pipeline as PipelineContract;

class AuthenticationController extends Controller
{
    /**
     * Attempt to authenticate a new session.
     *
     * @param \Citadel\Http\Requests\LoginRequest $request
     * @param \Citadel\Contracts\AuthenticatesUsers $authenticator
     * @return \Citadel\Http\Responses\LoginResponse
     */
    public function store(LoginRequest $request, AuthenticatesUsers $authenticator): LoginResponse
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
     * @return \Illuminate\Contracts\Pipeline\Pipeline
     */
    protected function sendThroughLoginPipeline(LoginRequest $request): PipelineContract
    {
        return (new Pipeline(app()))->send($request)->through(
            array_filter(Config::loginPipeline())
        );
    }
}
