<?php

namespace Emberfuse\Scorch\Http\Controllers;

use Illuminate\Http\Request;
use Emberfuse\Scorch\Scorch\Config;
use Illuminate\Contracts\Routing\ResponseFactory;
use Emberfuse\Scorch\Contracts\Responses\VerifyEmailViewResponse;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     *
     * @param \Illuminate\Http\Request                      $request
     * @param \Illuminate\Contracts\Routing\ResponseFactory $response
     *
     * @return \Symfony\Component\HttpFoundation\Response|Illuminate\Contracts\Support\Responsable
     */
    public function __invoke(Request $request, ResponseFactory $response)
    {
        return $request->user()->hasVerifiedEmail()
            ? $response->redirectToIntended(Config::home())
            : $this->resolve(VerifyEmailViewResponse::class);
    }
}
