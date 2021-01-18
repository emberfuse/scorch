<?php

namespace Citadel\Http\Responses;

use Citadel\Auth\Config;
use Illuminate\Routing\Redirector;
use Illuminate\Routing\ResponseFactory;

abstract class Response extends ResponseFactory
{
    /**
     * Full URL path to home route.
     *
     * @return string
     */
    public function home(): string
    {
        return url(Config::home(['/home']));
    }

    /**
     * Get instance of routin redirector class.
     *
     * @return \Illuminate\Routing\Redirector
     */
    public function redirect(): Redirector
    {
        return $this->redirector;
    }
}
