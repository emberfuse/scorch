<?php

namespace Cratespace\Citadel\Http\Responses;

use Illuminate\Routing\Redirector;
use Cratespace\Citadel\Limiters\LoginRateLimiter;
use Illuminate\View\Factory as ViewFactory;
use Illuminate\Contracts\Support\Responsable;

class LoginResponse extends Response implements Responsable
{
    /**
     * The login rate limiter instance.
     *
     * @var \Citadel\Limiters\LoginRateLimiter
     */
    protected $limiter;

    /**
     * Create a new class instance.
     *
     * @param \Illuminate\Contracts\View\Factory $view
     * @param \Illuminate\Routing\Redirector     $redirector
     * @param \Citadel\Limiters\LoginRateLimiter $limiter
     *
     * @return void
     */
    public function __construct(ViewFactory $view, Redirector $redirector, LoginRateLimiter $limiter)
    {
        parent::__construct($view, $redirector);

        $this->limiter = $limiter;
    }

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
            : $this->redirectToIntended($this->home(), 302);
    }
}
