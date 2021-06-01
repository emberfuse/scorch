<?php

namespace Emberfuse\Scorch\Http\Responses;

use Illuminate\Contracts\Support\Responsable;

class GenerateRecoveryCodesResponse extends Response implements Responsable
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
            ? $this->json()
            : $this->back()->with('status', 'recovery-codes-generated');
    }
}
