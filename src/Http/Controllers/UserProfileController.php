<?php

namespace Cratespace\Citadel\Http\Controllers;

use Illuminate\Http\Request;
use Cratespace\Citadel\Jobs\DeleteUserJob;
use Cratespace\Citadel\Http\Requests\DeleteUserRequest;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Support\Responsable;
use Cratespace\Citadel\Http\Responses\DeleteUserResponse;
use Symfony\Component\HttpFoundation\Response;
use Cratespace\Citadel\Contracts\Actions\UpdatesUserProfiles;
use Cratespace\Citadel\Http\Requests\UpdateUserProfileRequest;
use Cratespace\Citadel\Http\Responses\UpdateUserProfileResponse;
use Cratespace\Citadel\Contracts\Responses\UserProfileViewResponse;

class UserProfileController extends Controller
{
    /**
     * Show user profile view.
     *
     * @param \Illuminate\Http\Request                             $request
     * @param \Citadel\Contracts\Responses\UserProfileViewResponse $response
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function show(Request $request, UserProfileViewResponse $response): Response
    {
        return $response->toResponse($request);
    }

    /**
     * Update the user's profile information.
     *
     * @param \Citadel\Http\Requests\UpdateUserProfileRequest $request
     * @param \Citadel\Contracts\Actions\UpdatesUserProfiles  $updater
     *
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserProfileRequest $request, UpdatesUserProfiles $updater)
    {
        $updater->update($request->user(), $request->validated());

        return $this->app(UpdateUserProfileResponse::class);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Http\Requests\DeleteUserRequest     $request
     * @param \App\Auth\Contracts\DeletesUsers         $deletor
     * @param \Illuminate\Contracts\Auth\StatefulGuard $auth
     *
     * @return \Illuminate\Contracts\Support\Responsable
     */
    public function destroy(DeleteUserRequest $request, StatefulGuard $auth): Responsable
    {
        DeleteUserJob::dispatch($request->user()->fresh());

        $auth->logout();

        return $this->app(DeleteUserResponse::class);
    }
}
