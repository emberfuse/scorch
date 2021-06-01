<?php

namespace Emberfuse\Scorch\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use Emberfuse\Scorch\Scorch\Config;
use Emberfuse\Scorch\Http\Requests\VerifyEmailRequest;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     *
     * @param \Emberfuse\Scorch\Http\Requests\VerifyEmailRequest $request
     *
     * @return mixed
     */
    public function __invoke(VerifyEmailRequest $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return $this->sendResponse($request, 204);
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return $this->sendResponse($request, 202);
    }

    /**
     * Send response to email verification process.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $jsonStatus
     *
     * @return mixed
     */
    protected function sendResponse(Request $request, int $jsonStatus = 204)
    {
        return $request->expectsJson()
            ? response()->json('', $jsonStatus)
            : response()->redirectToIntended(
                Config::home() . '?verified=1'
            );
    }
}
