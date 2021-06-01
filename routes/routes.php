<?php

use Emberfuse\Scorch\Scorch\Config;
use Illuminate\Support\Facades\Route;
use Emberfuse\Scorch\Scorch\Route as ScorchRoute;
use Emberfuse\Scorch\Http\Controllers\PasswordController;
use Emberfuse\Scorch\Http\Controllers\CsrfCookieController;
use Emberfuse\Scorch\Http\Controllers\UserAddressController;
use Emberfuse\Scorch\Http\Controllers\UserProfileController;
use Emberfuse\Scorch\Http\Controllers\VerifyEmailController;
use Emberfuse\Scorch\Http\Controllers\RecoveryCodeController;
use Emberfuse\Scorch\Http\Controllers\RegisterUserController;
use Emberfuse\Scorch\Http\Controllers\PasswordResetController;
use Emberfuse\Scorch\Http\Controllers\AuthenticationController;
use Emberfuse\Scorch\Http\Controllers\ConfirmPasswordController;
use Emberfuse\Scorch\Http\Controllers\TwoFactorQrCodeController;
use Emberfuse\Scorch\Http\Controllers\UserProfilePhotoController;
use Emberfuse\Scorch\Http\Controllers\PasswordResetLinkController;
use Emberfuse\Scorch\Http\Controllers\OtherBrowserSessionsController;
use Emberfuse\Scorch\Http\Controllers\ConfirmPasswordStatusController;
use Emberfuse\Scorch\Http\Controllers\EmailVerificationPromptController;
use Emberfuse\Scorch\Http\Controllers\TwoFactorAuthenticationController;
use Emberfuse\Scorch\Http\Controllers\EmailVerificationNotificationController;
use Emberfuse\Scorch\Http\Controllers\TwoFactorAuthenticationStatusController;

Route::group([
    'middleware' => Config::middleware(['web']),
], function (): void {
    Route::group(['middleware' => ['guest:' . Config::guard('web')]], function (): void {
        if (ScorchRoute::isEnabled('register')) {
            Route::get('/register', [RegisterUserController::class, 'create'])->name('register');
            Route::post('/register', [RegisterUserController::class, 'store']);
        }

        if (ScorchRoute::isEnabled('login')) {
            Route::get('/login', [AuthenticationController::class, 'create'])->name('login');
            Route::post('/login', [AuthenticationController::class, 'store']);
        }

        if (ScorchRoute::isEnabled('two-factor-challenge')) {
            Route::get('/two-factor-challenge', [TwoFactorAuthenticationController::class, 'create'])->name('two-factor.login');
            Route::post('/two-factor-challenge', [TwoFactorAuthenticationController::class, 'store']);
        }

        if (ScorchRoute::isEnabled('forgot-password')) {
            Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
            Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
            Route::get('/reset-password/{token}', [PasswordResetController::class, 'create'])->name('password.reset');
            Route::post('/reset-password', [PasswordResetController::class, 'store'])->name('password.update');
        }
    });

    Route::group(['middleware' => ['auth']], function (): void {
        if (ScorchRoute::isEnabled('login')) {
            Route::post('/logout', [AuthenticationController::class, 'destroy'])->name('logout');
        }

        Route::group(['prefix' => 'user'], function (): void {
            Route::get('/confirm-password', [ConfirmPasswordController::class, 'show'])->name('password.confirm');
            Route::get('/confirmed-password-status', [ConfirmPasswordStatusController::class, '__invoke'])->name('password.confirmation');
            Route::post('/confirm-password', [ConfirmPasswordController::class, 'store']);

            Route::put('/password', [PasswordController::class, '__invoke'])->name('user-password.update');

            Route::get('/profile', [UserProfileController::class, 'show'])->name('user.show');
            Route::put('/profile', [UserProfileController::class, 'update'])->name('user.update');
            Route::delete('/profile', [UserProfileController::class, 'destroy'])->name('user.destroy');
            Route::delete('/profile-photo', [UserProfilePhotoController::class, '__invoke'])->name('user-photo.destroy');
            Route::put('/profile-address', [UserAddressController::class, 'update'])->name('user-address.update');

            Route::group(['middleware' => 'password.confirm'], function (): void {
                Route::post('/two-factor-authentication', [TwoFactorAuthenticationStatusController::class, 'store'])->name('two-factor.enable');
                Route::delete('/two-factor-authentication', [TwoFactorAuthenticationStatusController::class, 'destroy'])->name('two-factor.disable');
                Route::get('/two-factor-qr-code', [TwoFactorQrCodeController::class, '__invoke'])->name('two-factor.qr-code');
                Route::get('/two-factor-recovery-codes', [RecoveryCodeController::class, 'index'])->name('two-factor.recovery-code');
                Route::post('/two-factor-recovery-codes', [RecoveryCodeController::class, 'store']);
            });

            Route::delete('/other-browser-sessions', [OtherBrowserSessionsController::class, '__invoke'])->name('other-browser-sessions.destroy');
        });

        Route::get('/email/verify', [EmailVerificationPromptController::class, '__invoke'])->name('verification.notice');
        Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
            ->middleware(['signed', 'throttle:6,1'])
            ->name('verification.verify');
        Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, '__invoke'])
            ->middleware(['throttle:6,1'])
            ->name('verification.send');
    });

    Route::get('/csrf-cookie', [CsrfCookieController::class, '@show'])->name('api.auth');
});
