<?php

use Illuminate\Foundation\Support\Providers\RouteServiceProvider;

return [
    'guard' => 'web',

    'middleware' => ['web'],

    'passwords' => 'users',

    'username' => 'email',

    'email' => 'email',

    'home' => RouteServiceProvider::HOME,

    'limiters' => [
        'login' => null,
    ],

    'features' => [
        Features::registration(),
        Features::resetPasswords(),
        Features::emailVerification(),
        Features::updateProfileInformation(),
        Features::updatePasswords(),
        Features::deleteUser(),
        Features::twoFactorAuthentication(),
    ],
]
