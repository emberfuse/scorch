<?php

namespace Citadel\Http\Responses;

use Illuminate\Routing\Redirector;
use Illuminate\View\Factory as ViewFactory;
use Illuminate\Contracts\Support\Responsable;

class PasswordResetResponse extends Response implements Responsable
{
    /**
     * The response status language key.
     *
     * @var string
     */
    protected $status;

    /**
     * Create a new class instance.
     *
     * @param \Illuminate\Contracts\View\Factory $view
     * @param \Illuminate\Routing\Redirector     $redirector
     * @param string                             $status
     *
     * @return void
     */
    public function __construct(ViewFactory $view, Redirector $redirector, string $status)
    {
        parent::__construct($view, $redirector);

        $this->status = $status;
    }

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
            ? $this->json(['message' => trans($this->status)], 200)
            : $this->redirectToRoute('login')->with('status', trans($this->status));
    }
}
