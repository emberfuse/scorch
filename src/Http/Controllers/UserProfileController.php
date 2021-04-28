<?php

namespace Cratespace\Sentinel\Http\Controllers;

use Cratespace\Sentinel\Jobs\DeleteUserJob;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Support\Responsable;
use Symfony\Component\HttpFoundation\Response;
use Cratespace\Sentinel\Http\Requests\DeleteUserRequest;
use Cratespace\Sentinel\Http\Responses\DeleteUserResponse;
use Cratespace\Sentinel\Contracts\Actions\UpdatesUserProfiles;
use Cratespace\Sentinel\Http\Requests\UpdateUserProfileRequest;
use Cratespace\Sentinel\Http\Responses\UpdateUserProfileResponse;
use Cratespace\Sentinel\Contracts\Responses\UserProfileViewResponse;

class UserProfileController extends Controller
{
    /**
     * Show user profile view.
     *
     * @param \Illuminate\Http\Request                              $request
     * @param \Sentinel\Contracts\Responses\UserProfileViewResponse $response
     *
     * @return \Illuminate\Contracts\Support\Responsable
     */
    public function show(): Responsable
    {
        return $this->resolve(UserProfileViewResponse::class);
    }

    /**
     * Update the user's profile information.
     *
     * @param \Sentinel\Http\Requests\UpdateUserProfileRequest $request
     * @param \Sentinel\Contracts\Actions\UpdatesUserProfiles  $updater
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function update(UpdateUserProfileRequest $request, UpdatesUserProfiles $updater): Response
    {
        $updater->update($request->user(), $request->validated());

        return UpdateUserProfileResponse::dispatch();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Http\Requests\DeleteUserRequest     $request
     * @param \App\Auth\Contracts\DeletesUsers         $deletor
     * @param \Illuminate\Contracts\Auth\StatefulGuard $auth
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function destroy(DeleteUserRequest $request, StatefulGuard $auth): Response
    {
        DeleteUserJob::dispatch($request->user()->fresh());

        $auth->logout();

        return DeleteUserResponse::dispatch();
    }
}
