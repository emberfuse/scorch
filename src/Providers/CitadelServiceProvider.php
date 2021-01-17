<?php

namespace Citadel\Providers;

use Citadel\Auth\Config;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Auth\StatefulGuard;

class CitadelServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/citadel.php', 'citadel');

        $this->registerResponseBindings();
        $this->registerAuthGuard();
        $this->registerActions();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->configurePublishing();
        $this->configureRoutes();
    }

    /**
     * Register default authentication guard implementation.
     *
     * @return void
     */
    protected function registerAuthGuard(): void
    {
        $this->app->bind(
            StatefulGuard::class,
            fn () => Auth::guard(Config::guard(['null']))
        );
    }

    protected function registerResponseBindings(): void
    {
        //
    }

    /**
     * Register citadel action action classes.
     *
     * @return void
     */
    protected function registerActions(): void
    {
        collect(Config::actions())->each(
            fn ($action, $contract) => $this->app->singleton($contract, $action)
        );
    }

    /**
     * Configure the publishable resources offered by the package.
     *
     * @return void
     */
    protected function configurePublishing(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../stubs/citadel.php' => config_path('citadel.php'),
            ], 'citadel-config');

            $this->publishes([
                __DIR__.'/../../stubs/CitadelServiceProvider.php' => app_path('Providers/CitadelServiceProvider.php'),
            ], 'citadel-support');

            $this->publishes([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ], 'citadel-migrations');
        }
    }

    /**
     * Configure the routes offered by the application.
     *
     * @return void
     */
    protected function configureRoutes(): void
    {
        Route::group([
            'namespace' => 'Citadel\Http\Controllers',
            'domain' => Config::domain([null]),
            'prefix' => Config::prefix(),
        ], function (): void {
            $this->loadRoutesFrom(__DIR__.'/../../routes/routes.php');
        });
    }
}
