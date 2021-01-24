<?php

namespace Citadel\Citadel;

use Closure;
use Citadel\Http\Responses\ViewResponse;
use Illuminate\Contracts\Foundation\Application;
use Citadel\Contracts\Responses\LoginViewResponse;
use Illuminate\Contracts\View\View as ViewContract;

class View
{
    /**
     * Specify which view should be used as the login view.
     *
     * @param \Closure|string $view
     *
     * @return void
     */
    public static function login($view): void
    {
        static::registerView(LoginViewResponse::class, $view);
    }

    /**
     * Register given view response.
     *
     * @param string          $viewResponse
     * @param \Closure|string $view
     *
     * @return void
     */
    protected static function registerView(string $viewResponse, $view): void
    {
        app()->singleton($viewResponse, function ($app) use ($view) {
            if ($view instanceof Closure) {
                return new ViewResponse(call_user_func($view));
            }

            return static::showView($app, $view);
        });
    }

    /**
     * Return given view.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     * @param string                                       $view
     *
     * @return \Illuminate\Contracts\View\View
     */
    public static function showView(Application $app, string $view): ViewContract
    {
        return view($view, ['request' => $app['request']]);
    }
}
