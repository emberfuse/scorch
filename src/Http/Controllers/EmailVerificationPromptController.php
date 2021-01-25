<?php

namespace Cratespace\Citadel\Http\Controllers;

use Cratespace\Citadel\Citadel\Config;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Cratespace\Citadel\Contracts\Responses\VerifyEmailViewResponse;
use Illuminate\Contracts\Support\Responsable;

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
            : $this->app(VerifyEmailViewResponse::class)
    }
}
