<?php

use Citadel\Citadel\Config;
use Illuminate\Support\Facades\Route;
use Citadel\Http\Controllers\AuthenticationController;
use Citadel\Http\Controllers\TwoFactorAuthenticationController;

Route::group([
    'middleware' => Config::middleware(['web']),
], function (): void {
    Route::get('/login', [AuthenticationController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticationController::class, 'store']);
    Route::post('/logout', [AuthenticationController::class, 'destroy']);

    // Route::get('/two-factor-challenge', [TwoFactorAuthenticationController::class, 'create'])->name('two-factor.login');
    // Route::post('/two-factor-challenge', [TwoFactorAuthenticationController::class, 'store']);
});
