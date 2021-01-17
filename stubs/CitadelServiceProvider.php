<?php

namespace App\Providers;

use Citadel\Actions\AuthenticateUser;
use Citadel\Contracts\AuthenticatesUsers;
use Citadel\Http\Middleware\EnsureLoginIsNotThrottled;
use Citadel\Http\Middleware\RedirectIfTwoFactorAuthenticatable;
use Illuminate\Support\ServiceProvider;

class CitadelServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
