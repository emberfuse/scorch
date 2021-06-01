<?php

namespace Emberfuse\Scorch\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Auth\StatefulGuard;
use Emberfuse\Scorch\Http\Requests\LogoutOtherBrowserSessionsRequest;

class OtherBrowserSessionsController extends Controller
{
    /**
     * Logout from other browser sessions.
     *
     * @param \Emberfuse\Scorch\Http\Requests\LogoutOtherBrowserSessionsRequest $request
     * @param \Illuminate\Contracts\Auth\StatefulGuard                          $guard
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function __invoke(LogoutOtherBrowserSessionsRequest $request, StatefulGuard $guard)
    {
        $guard->logoutOtherDevices($request->password);

        $this->deleteOtherSessionRecords($request);

        return back(302);
    }

    /**
     * Delete the other browser session records from storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return void
     */
    protected function deleteOtherSessionRecords(Request $request): void
    {
        if (config('session.driver') !== 'database') {
            return;
        }

        DB::connection(config('session.connection'))->table(config('session.table', 'sessions'))
            ->where('user_id', $request->user()->getAuthIdentifier())
            ->where('id', '!=', $request->session()->getId())
            ->delete();
    }
}
