<?php

namespace Emberfuse\Scorch\Http\Controllers;

use Illuminate\Http\Request;
use Emberfuse\Scorch\Scorch\Config;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function __invoke(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return $request->wantsJson()
                ? response()->json('', 204)
                : redirect()->intended(Config::home());
        }

        $request->user()->sendEmailVerificationNotification();

        return $request->wantsJson()
            ? response()->json('', 202)
            : back()->with('status', 'verification-link-sent');
    }
}
