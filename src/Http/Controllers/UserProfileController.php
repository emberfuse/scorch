<?php

namespace Cratespace\Sentinel\Http\Controllers;

use Cratespace\Sentinel\Jobs\DeleteUserJob;
use Cratespace\Sentinel\Contracts\Actions\LogsoutUsers;
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
     * @return mixed
     */
    public function show()
    {
        return $this->resolve(UserProfileViewResponse::class);
    }

    /**
     * Update the user's profile information.
     *
     * @param \Sentinel\Http\Requests\UpdateUserProfileRequest $request
     * @param \Sentinel\Contracts\Actions\UpdatesUserProfiles  $updater
     *
     * @return mixed
     */
    public function update(UpdateUserProfileRequest $request, UpdatesUserProfiles $updater)
    {
        $updater->update($request->user(), $request->validated());

        return UpdateUserProfileResponse::dispatch();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Http\Requests\DeleteUserRequest                $request
     * @param \Cratespace\Sentinel\Contracts\Actions\LogsoutUsers $auth
     *
     * @return mixed
     */
    public function destroy(DeleteUserRequest $request, LogsoutUsers $auth)
    {
        DeleteUserJob::dispatch($request->user()->fresh());

        $auth->logout($request);

        return DeleteUserResponse::dispatch();
    }
}
