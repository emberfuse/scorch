<?php

namespace Citadel\Http\Responses;

use Illuminate\Contracts\Support\Responsable;

class DeleteUserResponse extends Response implements Responsable
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
        return $request->expectsJson() ? $this->noContent() : $this->redirectTo('/');
    }
}
