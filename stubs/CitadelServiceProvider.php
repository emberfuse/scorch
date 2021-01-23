<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class CitadelServiceProvider extends ServiceProvider
{
    /**
     * The citadel action classes.
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
