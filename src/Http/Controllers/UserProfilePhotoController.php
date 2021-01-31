<?php

namespace Cratespace\Citadel\Http\Controllers;

use Illuminate\Contracts\Support\Responsable;
use Cratespace\Citadel\Http\Requests\DeleteProfilePhotoRequest;
use Cratespace\Citadel\Http\Responses\DeleteProfilePhotoResponse;

class UserProfilePhotoController extends Controller
{
    /**
     * Delete the current user's profile photo.
     *
     * @param \App\Http\App\Http\Requests\DeleteProfilePhotoRequest $request
     *
     * @return \App\Http\Responses\DeleteProfilePhotoResponse
     */
    public function __invoke(DeleteProfilePhotoRequest $request): Responsable
    {
        $request->user()->deleteProfilePhoto();

        return $this->app(DeleteProfilePhotoResponse::class);
    }
}
