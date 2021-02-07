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
    'limiters' => [
        'login' => null,
        'two-factor' => null,
    ],
    'features' => [],
];
