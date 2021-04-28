<?php

namespace Cratespace\Sentinel\Http\Controllers;

use Symfony\Component\HttpFoundation\Response;
use Cratespace\Sentinel\Http\Requests\DeleteProfilePhotoRequest;
use Cratespace\Sentinel\Http\Responses\DeleteProfilePhotoResponse;

class UserProfilePhotoController extends Controller
{
    /**
     * Delete the current user's profile photo.
     *
     * @param \App\Http\App\Http\Requests\DeleteProfilePhotoRequest $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function __invoke(DeleteProfilePhotoRequest $request): Response
    {
        $request->user()->deleteProfilePhoto();

        return $this->resolve(DeleteProfilePhotoResponse::class);
    }
}
