<?php

namespace Citadel\Http\Controllers;

use Symfony\Component\HttpFoundation\Response;
use Citadel\Http\Requests\UpdatePasswordRequest;
use Citadel\Contracts\Actions\UpdatesUserPasswords;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     *
     * @param \Illuminate\Http\Request                        $request
     * @param \Laravel\Fortify\Contracts\UpdatesUserPasswords $updater
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function __invoke(UpdatePasswordRequest $request, UpdatesUserPasswords $updater): Response
    {
        $updater->update($request->user(), $request->all());

        return $request->wantsJson()
            ? response()->json()
            : back()->with('status', 'password-updated');
    }
}
