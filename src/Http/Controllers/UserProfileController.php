<?php

namespace Emberfuse\Scorch\Http\Controllers;

use Emberfuse\Scorch\Jobs\DeleteUserJob;
use Emberfuse\Scorch\Contracts\Actions\LogsoutUsers;
use Emberfuse\Scorch\Http\Requests\DeleteUserRequest;
use Emberfuse\Scorch\Http\Responses\DeleteUserResponse;
use Emberfuse\Scorch\Contracts\Actions\UpdatesUserProfiles;
use Emberfuse\Scorch\Http\Requests\UpdateUserProfileRequest;
use Emberfuse\Scorch\Http\Responses\UpdateUserProfileResponse;
use Emberfuse\Scorch\Contracts\Responses\UserProfileViewResponse;

class UserProfileController extends Controller
{
    /**
     * Show user profile view.
     *
     * @param \Illuminate\Http\Request                                      $request
     * @param \Emberfuse\Scorch\Contracts\Responses\UserProfileViewResponse $response
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
     * @param \Emberfuse\Scorch\Http\Requests\UpdateUserProfileRequest $request
     * @param \Emberfuse\Scorch\Contracts\Actions\UpdatesUserProfiles  $updater
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
     * @param \App\Http\Requests\DeleteUserRequest             $request
     * @param \Emberfuse\Scorch\Contracts\Actions\LogsoutUsers $auth
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
