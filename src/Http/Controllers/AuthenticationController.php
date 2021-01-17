<?php

namespace Citadel\Http\Controllers;

use Illuminate\Routing\Controller;
use Citadel\Http\Requests\LoginRequest;
use Citadel\Contracts\AuthenticatesUsers;
use Citadel\Http\Responses\LoginResponse;
use Symfony\Component\HttpFoundation\Response;

class AuthenticationController extends Controller
{
    /**
     * Attempt to authenticate a new session.
     *
     * @param \Citadel\Http\Requests\LoginRequest $request
     * @param \Citadel\Http\Responses\LoginResponse $response
     * @param \Citadel\Contracts\AuthenticatesUsers $authenticator
     * @return \Citadel\Http\Responses\Response
     */
    public function store(
        LoginRequest $request,
        LoginResponse $response,
        AuthenticatesUsers $authenticator
    ): Response {
        $authenticator->authenticate($request->validated());

        return $response->toResponse($request);
    }
}
