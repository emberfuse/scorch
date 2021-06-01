<?php

namespace Emberfuse\Scorch\Http\Controllers;

use Emberfuse\Scorch\Http\Requests\DeleteProfilePhotoRequest;
use Emberfuse\Scorch\Http\Responses\DeleteProfilePhotoResponse;

class UserProfilePhotoController extends Controller
{
    /**
     * Delete the current user's profile photo.
     *
     * @param \App\Http\App\Http\Requests\DeleteProfilePhotoRequest $request
     *
     * @return mixed
     */
    public function __invoke(DeleteProfilePhotoRequest $request)
    {
        $request->user()->deleteProfilePhoto();

        return $this->resolve(DeleteProfilePhotoResponse::class);
    }
}
