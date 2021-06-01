<?php

namespace Emberfuse\Scorch\Http\Responses;

use Illuminate\Contracts\Support\Responsable;

class UpdateUserPasswordResponse extends Response implements Responsable
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
            ? $this->json('', 200)
            : $this->back()->with('status', 'password-updated');
    }
}
