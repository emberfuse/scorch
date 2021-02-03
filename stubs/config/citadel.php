<?php

use App\Providers\RouteServiceProvider;

return [
    /*
     * Authentication Guard.
     */

    'guard' => 'web',

    /*
     * Citadel Password Broker.
     */
    'passwords' => 'users',

    /*
     * Username / Email.
     */
    'username' => 'email',
    'email' => 'email',

    /*
     * Home Path.
     */
    'home' => RouteServiceProvider::HOME,

    /*
     * Citadel Routes Prefix / Subdomain.
     */
    'prefix' => '',
    'domain' => null,

    /*
     * Citadel Routes Middleware
     */
    'middleware' => ['web'],

    /*
     * Rate Limiting.
     */
    'limiters' => [
        'login' => 'login',
        'two-factor' => 'two-factor',
    ],

    /*
     * Register View Routes.
     */
    'views' => true,
];
