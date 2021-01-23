<?php

namespace App\Actions\Citadel;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Auth\PasswordBroker;
use App\Citadel\Actions\Traits\PasswordUpdater;
use Citadel\Contracts\Actions\ResetsUserPasswords;

class ResetUserPassword implements ResetsUserPasswords
{
    use PasswordUpdater;

    /**
     * The password broker implementation.
     *
     * @var \Illuminate\Contracts\Auth\PasswordBroker
     */
    protected $broker;

    /**
     * The guard implementation.
     *
     * @var \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected $guard;

    /**
     * Create new instance of user password resetor.
     *
     * @param \Illuminate\Contracts\Auth\PasswordBroker $broker
     * @param \Illuminate\Contracts\Auth\StatefulGuard  $guard
     *
     * @return void
     */
    public function __construct(PasswordBroker $broker, StatefulGuard $guard)
    {
        $this->broker = $broker;
        $this->guard = $guard;
    }

    /**
     * Validate and reset the user's forgotten password.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function reset(Request $request)
    {
        return $this->broker->reset(
            $this->getInput($request),
            $this->resetPasswordCallback($request),
        );
    }

    /**
     * Get only the neccessary input values.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    protected function getInput(Request $request): array
    {
        return $request->only(
            'email',
            'password',
            'password_confirmation',
            'token'
        );
    }

    /**
     * Get callback used to actually reset the given user's password.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Closure
     */
    protected function resetPasswordCallback(Request $request): Closure
    {
        return function ($user) use ($request) {
            $this->updatePassword($user, $request->input('password'));

            event(new PasswordReset($user));

            // $this->guard->login($user);
        };
    }
}
