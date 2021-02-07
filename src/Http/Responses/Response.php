<?php

namespace Cratespace\Sentinel\Http\Responses;

use Illuminate\Routing\Redirector;
use Illuminate\Http\RedirectResponse;
use Cratespace\Sentinel\Sentinel\Config;
use Illuminate\Routing\ResponseFactory;
use Illuminate\View\Factory as ViewFactory;
use Illuminate\Contracts\Support\Responsable;

abstract class Response extends ResponseFactory
{
    /**
     * Optional content.
     *
     * @var mixed|null
     */
    protected $content;

    /**
     * Create a new response factory instance.
     *
     * @param \Illuminate\Contracts\View\Factory $view
     * @param \Illuminate\Routing\Redirector     $redirector
     * @param mixed|null                         $content
     *
     * @return void
     */
    public function __construct(
        ViewFactory $view,
        Redirector $redirector,
        $content = null
    ) {
        parent::__construct($view, $redirector);

        $this->content = $content;
    }

    /**
     * Dispatch response.
     *
     * @param mixed|null $content
     *
     * @return \Illuminate\Contracts\Support\Responsable|Illuminate\Routing\ResponseFactory
     */
    public static function dispatch($content = null)
    {
        $response = new static(
            app(ViewFactory::class),
            app(Redirector::class),
            $content
        );

        if ($response instanceof Responsable) {
            return $response->toResponse(request());
        }

        return $response;
    }

    /**
     * Full URL path to home route.
     *
     * @return string
     */
    public function home(): string
    {
        return url(Config::home(['/home']));
    }

    /**
     * Get instance of routin redirector class.
     *
     * @return \Illuminate\Routing\Redirector
     */
    public function redirect(): Redirector
    {
        return $this->redirector;
    }

    /**
     * Create a new redirect response to the previous location.
     *
     * @param int   $status
     * @param array $headers
     * @param mixed $fallback
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function back($status = 302, $headers = [], $fallback = false): RedirectResponse
    {
        return $this->redirect()->back($status, $headers, $fallback);
    }
}
