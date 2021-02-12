<?php

namespace Cratespace\Sentinel\Http\Responses;

use Illuminate\Routing\Redirector;
use Illuminate\View\Factory as ViewFactory;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Validation\ValidationException;

class FailedPasswordResetLinkRequestResponse extends Response implements Responsable
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
        if ($request->wantsJson()) {
            throw ValidationException::withMessages(['email' => [trans($this->content)]]);
        }

        return $this->redirect()->back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => trans($this->content)]);
    }
}
