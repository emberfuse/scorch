<?php

namespace Cratespace\Citadel\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\Auth\StatefulGuard;
use Cratespace\Citadel\Http\Requests\LogoutOtherBrowserSessionsRequest;

class OtherBrowserSessionsController extends Controller
{
    /**
     * Logout from other browser sessions.
     *
     * @param \Cratespace\Preflight\Http\Requests\LogoutOtherBrowserSessionsRequest $request
     * @param \Illuminate\Contracts\Auth\StatefulGuard                              $guard
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function __invoke(LogoutOtherBrowserSessionsRequest $request, StatefulGuard $guard): RedirectResponse
    {
        $guard->logoutOtherDevices($request->password);

        $this->deleteOtherSessionRecords($request);

        return back(303);
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
