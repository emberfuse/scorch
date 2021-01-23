<?php

namespace App\Providers;

use Citadel\Auth\AttemptToAuthenticate;
use Illuminate\Support\ServiceProvider;
use Citadel\Auth\EnsureLoginIsNotThrottled;
use Citadel\Auth\PrepareAuthenticatedSession;
use Citadel\Auth\RedirectIfTwoFactorAuthenticatable;

class CitadelServiceProvider extends ServiceProvider
{
    /**
     * The authentication pipeline classes.
     *
     * @var array
     */
    protected static $actions = [];

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerActions();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Register all citadel action classes to the service container.
     *
     * @return void
     */
    protected function registerActions(): void
    {
        collect(static::$actions)->each(
            fn ($abstract, $concrete) => $this->app->singleton($abstract, $concrete)
        );
    }
}
