<?php

namespace Cratespace\Citadel\Http\Controllers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Support\Responsable;
use Cratespace\Citadel\Http\Requests\RegisterRequest;
use Cratespace\Citadel\Http\Responses\RegisterResponse;
use Cratespace\Citadel\Contracts\Actions\CreatesNewUsers;
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
     * @return \Illuminate\Contracts\Support\Responsable
     */
    public function create(): Responsable
    {
        return $this->app(RegisterViewResponse::class);
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
