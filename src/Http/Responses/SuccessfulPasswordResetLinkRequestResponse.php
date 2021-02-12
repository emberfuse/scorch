<?php

namespace Cratespace\Sentinel\Http\Responses;

use Illuminate\Routing\Redirector;
use Illuminate\View\Factory as ViewFactory;
use Illuminate\Contracts\Support\Responsable;

class SuccessfulPasswordResetLinkRequestResponse extends Response implements Responsable
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
            : $this->redirect()->back()->with('status', trans($this->content));
    }
}
