<?php

namespace Citadel\Providers;

use Citadel\Citadel\Config;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Auth\StatefulGuard;
use Citadel\Contracts\Providers\TwoFactorAuthenticationProvider as TwoFactorAuthenticationProviderContract;

class CitadelServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/citadel.php', 'citadel');

        $this->registerAuthGuard();

        $this->registerTwoFactorAuthProvider();
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

    /**
     * Register two factor authentication provider.
     *
     * @return void
     */
    protected function registerTwoFactorAuthProvider(): void
    {
        $this->app->singleton(
            TwoFactorAuthenticationProviderContract::class,
            TwoFactorAuthenticationProviderContract::class
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
                __DIR__ . '../../config/citadel.php' => config_path('citadel.php'),
            ], 'citadel-config');

            $this->publishes([
                __DIR__ . '../../stubs/CitadelServiceProvider.php' => app_path('Providers/CitadelServiceProvider.php'),
            ], 'citadel-support');

            $this->publishes([
                __DIR__ . '../../database/migrations' => database_path('migrations'),
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
            $this->loadRoutesFrom(__DIR__ . '/../../routes/routes.php');
        });
    }
}
