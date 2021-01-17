<?php

use Citadel\Citadel\Features;
use Citadel\Actions\AuthenticateUser;
use App\Providers\RouteServiceProvider;
use Citadel\Contracts\AuthenticatesUsers;
use Citadel\Http\Middleware\EnsureLoginIsNotThrottled;
use Citadel\Http\Middleware\RedirectIfTwoFactorAuthenticatable;

return [
    'guard' => 'web',

    'middleware' => ['web'],

    'prefix' => '',

    'domain' => null,

    'passwords' => 'users',

    'username' => 'email',

    'email' => 'email',

    'home' => '/home',

    'login_pipeline' => [
        EnsureLoginIsNotThrottled::class,
        RedirectIfTwoFactorAuthenticatable::class,
    ],

    'limiters' => [
        'login' => null,
    ],

    'features' => [
        // Features::registration(),
        // Features::resetPasswords(),
        // Features::emailVerification(),
        // Features::updateProfileInformation(),
        // Features::updatePasswords(),
        // Features::deleteUser(),
        // Features::twoFactorAuthentication(),
    ],

    'actions' => [
        AuthenticatesUsers::class => AuthenticateUser::class
    ],
];
