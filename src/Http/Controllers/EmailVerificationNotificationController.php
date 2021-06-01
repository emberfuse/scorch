<?php

namespace Emberfuse\Scorch\Http\Controllers;

use Illuminate\Http\Request;
use Emberfuse\Scorch\Scorch\Config;
use Illuminate\Contracts\Routing\ResponseFactory;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     *
     * @param \Illuminate\Http\Request                      $request
     * @param \Illuminate\Contracts\Routing\ResponseFactory $response
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function __invoke(Request $request, ResponseFactory $response)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return $request->wantsJson()
                ? $response->json('', 204)
                : $response->redirectToIntended(Config::home());
        }

        $request->user()->sendEmailVerificationNotification();

        return $request->wantsJson()
            ? $response->json('', 202)
            : back()->with('status', 'verification-link-sent');
    }
}
