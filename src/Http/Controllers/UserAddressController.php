<?php

namespace Emberfuse\Scorch\Http\Controllers;

use App\Actions\Auth\UpdateUserAddress;
use Emberfuse\Scorch\Http\Requests\UpdateUserAddressRequest;
use Emberfuse\Scorch\Http\Responses\UpdateUserAddressResponse;

class UserAddressController extends Controller
{
    /**
     * Update the user's profile information.
     *
     * @param \Emberfuse\Scorch\Http\Requests\UpdateUserAddressRequest $request
     * @param \Emberfuse\Scorch\Contracts\Actions\UpdateUserAddress    $updater
     *
     * @return mixed
     */
    public function update(UpdateUserAddressRequest $request, UpdateUserAddress $updater)
    {
        $updater->update($request->user(), $request->validated());

        return UpdateUserAddressResponse::dispatch();
    }
}
