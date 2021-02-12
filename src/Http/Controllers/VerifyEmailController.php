<?php

namespace Cratespace\Sentinel\Http\Controllers;

use Illuminate\Auth\Events\Verified;
use Cratespace\Sentinel\Sentinel\Config;
use Symfony\Component\HttpFoundation\Response;
use Cratespace\Sentinel\Http\Requests\VerifyEmailRequest;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     *
     * @param \Sentinel\Http\Requests\VerifyEmailRequest $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function __invoke(VerifyEmailRequest $request): Response
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(Config::home() . '?verified=1');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return redirect()->intended(Config::home() . '?verified=1');
    }
}
