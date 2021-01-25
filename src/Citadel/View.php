<?php

namespace Citadel\Citadel;

use Closure;
use Citadel\Http\Responses\ViewResponse;
use Illuminate\Contracts\Foundation\Application;
use Citadel\Contracts\Responses\LoginViewResponse;
use Illuminate\Contracts\View\View as ViewContract;
use Citadel\Contracts\Responses\RegisterViewResponse;
use Citadel\Contracts\Responses\ResetPasswordViewResponse;
use Citadel\Contracts\Responses\RequestPasswordResetLinkViewResponse;

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
     * Specify which view should be used as the register view.
     *
     * @param \Closure|string $view
     *
     * @return void
     */
    public static function register($view): void
    {
        static::registerView(RegisterViewResponse::class, $view);
    }

    /**
     * Specify which view should be used as the reset password link request view.
     *
     * @param \Closure|string $view
     *
     * @return void
     */
    public static function requestPasswordResetLink($view): void
    {
        static::registerView(RequestPasswordResetLinkViewResponse::class, $view);
    }

    /**
     * Specify which view should be used as the reset password view.
     *
     * @param \Closure|string $view
     *
     * @return void
     */
    public static function resetPassword($view): void
    {
        static::registerView(ResetPasswordViewResponse::class, $view);
    }

    /**
     * Specify which view should be used as the verify email view.
     *
     * @param \Closure|string $view
     *
     * @return void
     */
    public static function verifyEmail($view): void
    {
        static::registerView(VerifyEmailViewResponse::class, $view);
    }

    /**
     * Specify which view should be used as the user profile view.
     *
     * @param \Closure|string $view
     *
     * @return void
     */
    public static function userProfile($view): void
    {
        static::registerView(UserProfileViewResponse::class, $view);
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
