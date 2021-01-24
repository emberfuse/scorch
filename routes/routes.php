<?php

use Citadel\Citadel\Config;
use Illuminate\Support\Facades\Route;
use Citadel\Http\Controllers\PasswordController;
use Citadel\Http\Controllers\UserProfileController;
use Citadel\Http\Controllers\VerifyEmailController;
use Citadel\Http\Controllers\RegisterUserController;
use Citadel\Http\Controllers\PasswordResetController;
use Citadel\Http\Controllers\AuthenticationController;
use Citadel\Http\Controllers\ConfirmPasswordController;
use Citadel\Http\Controllers\PasswordResetLinkController;
use Citadel\Http\Controllers\ConfirmPasswordStatusController;
use Citadel\Http\Controllers\EmailVerificationPromptController;
use Citadel\Http\Controllers\TwoFactorAuthenticationController;
use Citadel\Http\Controllers\EmailVerificationNotificationController;
use Citadel\Http\Controllers\TwoFactorAuthenticationStatusController;

Route::group([
    'middleware' => Config::middleware(['web']),
], function (): void {
    Route::group(['middleware' => ['guest']], function (): void {
        Route::get('/register', [RegisterUserController::class, 'create'])->name('register');
        Route::post('/register', [RegisterUserController::class, 'store']);

        Route::get('/login', [AuthenticationController::class, 'create'])->name('login');
        Route::post('/login', [AuthenticationController::class, 'store']);

        Route::get('/two-factor-challenge', [TwoFactorAuthenticationController::class, 'create'])->name('two-factor.login');
        Route::post('/two-factor-challenge', [TwoFactorAuthenticationController::class, 'store']);

        Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
        Route::get('/reset-password/{token}', [PasswordResetController::class, 'create'])->name('password.reset');
        Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
        Route::post('/reset-password', [PasswordResetController::class, 'store'])->name('password.update');
    });

    Route::group(['middleware' => ['auth']], function (): void {
        Route::post('/logout', [AuthenticationController::class, 'destroy']);

        Route::group(['prefix' => 'user'], function (): void {
            Route::post('/two-factor-authentication', [TwoFactorAuthenticationStatusController::class, 'store']);
            Route::delete('/two-factor-authentication', [TwoFactorAuthenticationStatusController::class, 'destroy']);

            Route::get('/confirm-password', [ConfirmPasswordController::class, 'show'])->name('password.confirm');
            Route::get('/confirmed-password-status', [ConfirmPasswordStatusController::class, '__invoke'])->name('password.confirmation');
            Route::post('/confirm-password', [ConfirmPasswordController::class, 'store']);

            Route::put('/password', [PasswordController::class, '__invoke'])->name('user-password.update');

            Route::get('/profile', [UserProfileController::class, 'show'])->name('user.show');
            Route::put('/profile', [UserProfileController::class, 'update'])->name('user.update');
            Route::delete('/profile', [UserProfileController::class, 'destroy'])->name('user.destroy');
        });

        Route::get('/email/verify', [EmailVerificationPromptController::class, '__invoke'])->name('verification.notice');
        Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
            ->middleware(['signed', 'throttle:6,1'])
            ->name('verification.verify');
        Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, '__invoke'])
            ->middleware(['throttle:6,1'])
            ->name('verification.send');
    });
});
