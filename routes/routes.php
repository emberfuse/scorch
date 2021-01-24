<?php

use Citadel\Citadel\Config;
use Illuminate\Support\Facades\Route;
use Citadel\Http\Controllers\AuthenticationController;
use Citadel\Http\Controllers\ConfirmPasswordController;
use Citadel\Http\Controllers\ConfirmPasswordStatusController;
use Citadel\Http\Controllers\TwoFactorAuthenticationController;
use Citadel\Http\Controllers\TwoFactorAuthenticationStatusController;

Route::group([
    'middleware' => Config::middleware(['web']),
], function (): void {
    Route::group(['middleware' => ['guest']], function (): void {
        Route::get('/login', [AuthenticationController::class, 'create'])->name('login');
        Route::post('/login', [AuthenticationController::class, 'store']);

        Route::get('/two-factor-challenge', [TwoFactorAuthenticationController::class, 'create'])->name('two-factor.login');
        Route::post('/two-factor-challenge', [TwoFactorAuthenticationController::class, 'store']);
    });

    Route::group(['middleware' => ['auth']], function (): void {
        Route::post('/logout', [AuthenticationController::class, 'destroy']);

        Route::group(['prefix' => 'user'], function (): void {
            Route::post('/two-factor-authentication', [TwoFactorAuthenticationStatusController::class, 'store']);
            Route::delete('/two-factor-authentication', [TwoFactorAuthenticationStatusController::class, 'destroy']);

            Route::get('/confirm-password', [ConfirmPasswordController::class, 'show'])->name('password.confirm');
            Route::get('/confirmed-password-status', [ConfirmPasswordStatusController::class, '__invoke'])->name('password.confirmation');
            Route::post('/confirm-password', [ConfirmPasswordController::class, 'store']);
        });
    });
});
