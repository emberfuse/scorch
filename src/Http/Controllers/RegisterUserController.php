<?php

namespace Cratespace\Sentinel\Http\Controllers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Support\Responsable;
use Symfony\Component\HttpFoundation\Response;
use Cratespace\Sentinel\Http\Requests\RegisterRequest;
use Cratespace\Sentinel\Http\Responses\RegisterResponse;
use Cratespace\Sentinel\Contracts\Actions\CreatesNewUsers;
use Cratespace\Sentinel\Contracts\Responses\RegisterViewResponse;

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
     * @param \Illuminate\Http\Request                           $request
     * @param \Sentinel\Contracts\Responses\RegisterViewResponse $response
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
     * @param \Sentinel\Http\Requests\RegisterRequest    $request
     * @param \Laravel\Fortify\Contracts\CreatesNewUsers $creator
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function store(RegisterRequest $request, CreatesNewUsers $creator): Response
    {
        event(new Registered($user = $creator->create($request->validated())));

        $this->guard->login($user);

        return RegisterResponse::dispatch();
    }
}
