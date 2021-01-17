<?php

use Citadel\Auth\Config;
use Illuminate\Support\Facades\Route;
use Citadel\Http\Controllers\AuthenticationController;

Route::group([
    'middleware' => Config::middleware(['web'])
], function () {
    Route::post('/login', [AuthenticationController::class, 'store']);
});
