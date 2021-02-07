<?php

use App\Providers\RouteServiceProvider;

return [
    /*
     * Authentication Guard.
     */

    'guard' => 'web',

    /*
     * Sentinel Password Broker.
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
     * Sentinel Routes Prefix / Subdomain.
     */
    'prefix' => '',
    'domain' => null,

    /*
     * Sentinel Routes Middleware
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
