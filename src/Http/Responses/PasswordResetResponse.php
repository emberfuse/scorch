<?php

namespace Emberfuse\Scorch\Http\Responses;

use Illuminate\Contracts\Support\Responsable;

class PasswordResetResponse extends Response implements Responsable
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
        return $request->wantsJson()
            ? $this->json(['message' => trans($this->content)], 200)
            : $this->redirectToRoute('login')->with('status', trans($this->content));
    }
}
