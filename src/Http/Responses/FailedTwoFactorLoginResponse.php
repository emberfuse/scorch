<?php

namespace Cratespace\Sentinel\Http\Responses;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Validation\ValidationException;

class FailedTwoFactorLoginResponse extends Response implements Responsable
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
        $message = __('The provided two factor authentication code was invalid.');

        if ($request->wantsJson()) {
            throw ValidationException::withMessages(['code' => [$message]]);
        }

        return $this->redirectToRoute('login')->withErrors(['code' => $message]);
    }
}
