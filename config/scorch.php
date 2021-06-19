<?php

return [
    'guard' => 'web',
    'middleware' => ['web'],
    'prefix' => '',
    'domain' => null,
    'passwords' => 'users',
    'username' => 'email',
    'email' => 'email',
    'views' => true,
    'home' => '/home',
    'expiration' => null,
    'limiters' => [
        'login' => null,
        'two-factor' => null,
    ],
    'auth_routes' => [
        'login' => true,
        'register' => true,
        'forgot-password' => true,
        'two-factor-challenge' => true,
    ],
    'login_pipeline' => [],
    'stateful' => explode(',', env('STATEFUL_DOMAINS', sprintf(
        '%s%s',
        'localhost,localhost:3000,127.0.0.1,127.0.0.1:8000,::1',
        env('APP_URL') ? ',' . parse_url(env('APP_URL'), \PHP_URL_HOST) : ''
    ))),

    /*
     * When authenticating your first-party SPA with API you may need to
     * customize some of the middleware API uses while processing the
     * request. You may change the middleware listed below as required.
     */
    'stateful_middleware' => [
        'verify_csrf_token' => App\Http\Middleware\VerifyCsrfToken::class,
        'encrypt_cookies' => App\Http\Middleware\EncryptCookies::class,
    ],
];
