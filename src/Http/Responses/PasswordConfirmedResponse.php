<?php

namespace Cratespace\Citadel\Http\Responses;

use Illuminate\Contracts\Support\Responsable;

class PasswordConfirmedResponse extends Response implements Responsable
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
        $request->session()->passwordConfirmed();

        return $request->expectsJson()
            ? $this->json('', 201)
            : $this->redirectToIntended($this->home(), 302);
    }
}
