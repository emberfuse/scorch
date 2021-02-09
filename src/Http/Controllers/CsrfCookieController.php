<?php

namespace Cratespace\Sentinel\Http\Controllers;

use Illuminate\Http\Response;

class CsrfCookieController
{
    /**
     * Return an empty response simply to trigger the storage of the CSRF cookie in the browser.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(): Response
    {
        return new Response('', 204);
    }
}
