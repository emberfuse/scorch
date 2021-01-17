<?php

namespace Citadel\Http\Responses;

use Citadel\Auth\Config;
use Illuminate\Routing\Redirector;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\ResponseFactory;

abstract class Response extends ResponseFactory
{
    /**
     * Get instance of route redirector.
     *
     * @return \Illuminate\Routing\Redirector
     */
    public function redirect(): Redirector
    {
        return $this->redirector;
    }

    /**
     * Create a new redirect response to the previous location.
     *
     * @param int $status
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function back(int $status = 302): RedirectResponse
    {
        return $this->redirect()->back($status);
    }

    /**
     * Default home URL.
     *
     * @return string
     */
    public function home(): string
    {
        return url(Config::home());
    }

    /**
     * Create a new redirect response to the "home" route.
     *
     * @param int $status
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toHome(int $status = 302): RedirectResponse
    {
        return $this->redirect()->to($this->home(), $status);
    }
}
