<?php

namespace Cratespace\Citadel\Http\Responses;

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
        if (! is_callable($this->content) || is_string($this->content)) {
            return view($this->content, ['request' => $request]);
        }

        $response = call_user_func($this->content, $request);

        if ($response instanceof Responsable) {
            return $response->toResponse($request);
        }

        return $response;
    }
}
