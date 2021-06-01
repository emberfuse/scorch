<?php

namespace Emberfuse\Scorch\Http\Controllers;

use Illuminate\Http\Request;
use Emberfuse\Scorch\Scorch\Config;
use Emberfuse\Scorch\Contracts\Responses\VerifyEmailViewResponse;

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
