<?php

namespace Cratespace\Citadel\Http\Controllers;

use Illuminate\Http\Request;
use Cratespace\Citadel\Citadel\Config;
use Cratespace\Citadel\Contracts\Responses\VerifyEmailViewResponse;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     *
     * @param \Illuminate\Http\Request                             $request
     * @param \Citadel\Contracts\Responses\VerifyEmailViewResponse $response
     *
     * @return \Symfony\Component\HttpFoundation\Response|Illuminate\Contracts\Support\Responsable
     */
    public function __invoke(Request $request, VerifyEmailViewResponse $response)
    {
        return $request->user()->hasVerifiedEmail()
            ? redirect()->intended(Config::home())
            : $this->app(VerifyEmailViewResponse::class);
    }
}
