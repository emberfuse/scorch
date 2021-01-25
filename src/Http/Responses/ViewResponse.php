<?php

namespace Citadel\Http\Responses;

use Illuminate\Contracts\Support\Responsable;

class ViewResponse implements Responsable
{
    /**
     * The view to be returned.
     *
     * @var string
     */
    protected $viewName;

    /**
     * Create a new class instance.
     *
     * @param \Closure|string $content
     *
     * @return void
     */
    public function __construct($content)
    {
        $this->content = $content;
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
            ? response()->json($this->content)
            : $this->content;
    }
}
