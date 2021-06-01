<?php

namespace Emberfuse\Scorch\Http\Controllers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Support\Responsable;
use Emberfuse\Scorch\Http\Requests\RegisterRequest;
use Emberfuse\Scorch\Http\Responses\RegisterResponse;
use Emberfuse\Scorch\Contracts\Actions\CreatesNewUsers;
use Emberfuse\Scorch\Contracts\Responses\RegisterViewResponse;

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
     * @param \Emberfuse\Scorch\Contracts\Responses\RegisterViewResponse $response
     *
     * @return \Illuminate\Contracts\Support\Responsable
     */
    public function create()
    {
        return $this->resolve(RegisterViewResponse::class);
    }

    /**
     * Create a new registered user.
     *
     * @param \Emberfuse\Scorch\Http\Requests\RegisterRequest    $request
     * @param \Emberfuse\Scorch\Contracts\CreatesNewUsers $creator
     *
     * @return mixed
     */
    public function store(RegisterRequest $request, CreatesNewUsers $creator)
    {
        event(new Registered(
            $user = $creator->create($request->validated())
        ));

        $this->guard->login($user);

        return RegisterResponse::dispatch($user);
    }
}
