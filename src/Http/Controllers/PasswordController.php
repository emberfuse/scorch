<?php

namespace Emberfuse\Scorch\Http\Controllers;

use Emberfuse\Scorch\Http\Requests\UpdatePasswordRequest;
use Emberfuse\Scorch\Contracts\Actions\UpdatesUserPasswords;
use Emberfuse\Scorch\Http\Responses\UpdateUserPasswordResponse;

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
        $updater->update($request->user(), $request->validated());

        return UpdateUserPasswordResponse::dispatch();
    }
}
