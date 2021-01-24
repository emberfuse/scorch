<?php

namespace Citadel\Providers;

use Citadel\Citadel\Config;
use Citadel\Actions\ConfirmPassword;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Auth\StatefulGuard;
use Citadel\Contracts\Actions\ConfirmsPasswords;
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
        $this->registerInternalActions();
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
            TwoFactorAuthenticationProvider::class
        );
    }

    /**
     * Register all citadel internal action classes.
     *
     * @return void
     */
    protected function registerInternalActions(): void
    {
        $this->app->singleton(ConfirmsPasswords::class, ConfirmPassword::class);
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
                __DIR__ . '/../../stubs/citadel.php' => config_path('citadel.php'),
            ], 'citadel-config');

            $this->publishes([
                __DIR__ . '/../../stubs/rules.php' => config_path('rules.php'),
            ], 'rules-config');

            $this->publishes([
                __DIR__ . '/../../stubs/AuthenticateUser.php' => app_path('Actions/Citadel/AuthenticateUser.php'),
                __DIR__ . '/../../stubs/CreateNewUser.php' => app_path('Actions/Citadel/CreateNewUser.php'),
                __DIR__ . '/../../stubs/DeleteUser.php' => app_path('Actions/Citadel/DeleteUser.php'),
                __DIR__ . '/../../stubs/ResetPassword.php' => app_path('Actions/Citadel/ResetPassword.php'),
                __DIR__ . '/../../stubs/UpdateUserPassword.php' => app_path('Actions/Citadel/UpdateUserPassword.php'),
                __DIR__ . '/../../stubs/UpdateUserProfile.php' => app_path('Actions/Citadel/UpdateUserProfile.php'),
                __DIR__ . '/../../stubs/PasswordUpdater.php' => app_path('Actions/Citadel/Traits/PasswordUpdater.php'),
                __DIR__ . '/../../stubs/CitadelServiceProvider.php' => app_path('Providers/CitadelServiceProvider.php'),
            ], 'citadel-support');

            $this->publishes([
                __DIR__ . '/../../database/migrations/2014_10_12_000000_create_users_table.php' => database_path('migrations/2014_10_12_000000_create_users_table.php'),
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
