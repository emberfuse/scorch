<?php

namespace Cratespace\Sentinel\Http\Controllers;

use App\Actions\Auth\UpdateUserAddress;
use Cratespace\Sentinel\Http\Requests\UpdateUserAddressRequest;
use Cratespace\Sentinel\Http\Responses\UpdateUserAddressResponse;

class UserAddressController extends Controller
{
    /**
     * Update the user's profile information.
     *
     * @param \Sentinel\Http\Requests\UpdateUserAddressRequest $request
     * @param \Sentinel\Contracts\Actions\UpdateUserAddress    $updater
     *
     * @return mixed
     */
    public function update(UpdateUserAddressRequest $request, UpdateUserAddress $updater)
    {
        $updater->update($request->user(), $request->validated());

        return UpdateUserAddressResponse::dispatch();
    }
}
