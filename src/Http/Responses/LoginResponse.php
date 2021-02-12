<?php

namespace Cratespace\Sentinel\Http\Responses;

use Illuminate\Routing\Redirector;
use Cratespace\Sentinel\Limiters\LoginRateLimiter;
use Illuminate\View\Factory as ViewFactory;
use Illuminate\Contracts\Support\Responsable;

class LoginResponse extends Response implements Responsable
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        return $request->expectsJson()
            ? $this->json(['two_factor' => false])
            : $this->redirectToIntended($this->home());
    }
}
