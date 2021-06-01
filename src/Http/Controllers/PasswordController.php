<?php

namespace Emberfuse\Scorch\Http\Controllers;

use Symfony\Component\HttpFoundation\Response;
use Emberfuse\Scorch\Http\Requests\UpdatePasswordRequest;
use Emberfuse\Scorch\Contracts\Actions\UpdatesUserPasswords;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     *
     * @param \Illuminate\Http\Request                                 $request
     * @param \Emberfuse\Scorch\Contracts\Actions\UpdatesUserPasswords $updater
     *
     * @return mixed
     */
    public function __invoke(UpdatePasswordRequest $request, UpdatesUserPasswords $updater)
    {
        $updater->update($request->user(), $request->all());

        return $request->wantsJson()
            ? response()->json()
            : back()->with('status', 'password-updated');
    }
}
