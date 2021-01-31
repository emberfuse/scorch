<?php

namespace Cratespace\Citadel\Citadel;

use Closure;
use Illuminate\Contracts\Foundation\Application;
use Cratespace\Citadel\Http\Responses\ViewResponse;
use Cratespace\Citadel\Contracts\Responses\LoginViewResponse;
use Illuminate\Contracts\View\View as IlluminateViewContract;
use Cratespace\Citadel\Contracts\Support\View as ViewContract;
use Cratespace\Citadel\Contracts\Responses\RegisterViewResponse;
use Cratespace\Citadel\Contracts\Responses\UserProfileViewResponse;
use Cratespace\Citadel\Contracts\Responses\ResetPasswordViewResponse;
use Cratespace\Citadel\Http\Responses\TwoFactorChallengeViewResponse;
use Cratespace\Citadel\Contracts\Responses\RequestPasswordResetLinkViewResponse;

class View implements ViewContract
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
     * Specify which view should be used as the two-factor auth challenge view.
     *
     * @param \Closure|string $view
     *
     * @return void
     */
    public static function twoFactorChallenge($view): void
    {
        static::registerView(TwoFactorChallengeViewResponse::class, $view);
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
    public static function registerView(string $viewResponse, $view): void
    {
        app()->singleton($viewResponse, function ($app) use ($view) {
            if ($view instanceof Closure) {
                return new ViewResponse($view);
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
    public static function showView(Application $app, string $view): IlluminateViewContract
    {
        return view($view, ['request' => $app['request']]);
    }
}
