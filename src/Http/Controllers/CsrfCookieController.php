<?php

namespace Emberfuse\Scorch\Http\Controllers;

use Illuminate\Contracts\Routing\ResponseFactory;

class CsrfCookieController
{
    /**
     * Return an empty response simply to trigger the storage of the CSRF cookie in the browser.
     *
     * @param \Illuminate\Contracts\Routing\ResponseFactory
     *
     * @return \Illuminate\Http\Response
     */
    public function show(ResponseFactory $response)
    {
        return $response->noContent();
    }
}
