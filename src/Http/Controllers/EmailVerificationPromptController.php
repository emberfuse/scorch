<?php

namespace Cratespace\Sentinel\Http\Controllers;

use Illuminate\Http\Request;
use Cratespace\Sentinel\Sentinel\Config;
use Cratespace\Sentinel\Contracts\Responses\VerifyEmailViewResponse;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response|Illuminate\Contracts\Support\Responsable
     */
    public function __invoke(Request $request)
    {
        return $request->user()->hasVerifiedEmail()
            ? redirect()->intended(Config::home())
            : $this->resolve(VerifyEmailViewResponse::class);
    }
}
