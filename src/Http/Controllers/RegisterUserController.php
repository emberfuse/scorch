<?php

namespace Cratespace\Citadel\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use Cratespace\Citadel\Http\Requests\RegisterRequest;
use Cratespace\Citadel\Http\Responses\RegisterResponse;
use Illuminate\Contracts\Auth\StatefulGuard;
use Cratespace\Citadel\Contracts\Actions\CreatesNewUsers;
use Symfony\Component\HttpFoundation\Response;
use Cratespace\Citadel\Contracts\Responses\RegisterViewResponse;

class RegisterUserController extends Controller
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
     * Show the registration view.
     *
     * @param \Illuminate\Http\Request                          $request
     * @param \Citadel\Contracts\Responses\RegisterViewResponse $response
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function create(Request $request, RegisterViewResponse $response): Response
    {
        return $response->toResponse($request);
    }

    /**
     * Create a new registered user.
     *
     * @param \Citadel\Http\Requests\RegisterRequest     $request
     * @param \Laravel\Fortify\Contracts\CreatesNewUsers $creator
     *
     * @return \Citadel\Http\Responses\RegisterResponse
     */
    public function store(RegisterRequest $request, CreatesNewUsers $creator): RegisterResponse
    {
        event(new Registered($user = $creator->create($request->validated())));

        $this->guard->login($user);

        return $this->app(RegisterResponse::class);
    }
}
