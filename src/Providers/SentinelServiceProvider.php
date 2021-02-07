<?php

namespace Cratespace\Sentinel\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Cratespace\Sentinel\Sentinel\Config;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Auth\StatefulGuard;
use Cratespace\Sentinel\Console\InstallCommand;
use Cratespace\Sentinel\Actions\ConfirmPassword;
use Cratespace\Sentinel\Console\MakeResponseCommand;
use Cratespace\Sentinel\Contracts\Actions\ConfirmsPasswords;
use Cratespace\Sentinel\Contracts\Providers\TwoFactorAuthenticationProvider as TwoFactorAuthenticationProviderContract;

class SentinelServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/sentinel.php', 'sentinel');

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
        $this->configureCommands();
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
     * Register all sentinel internal action classes.
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
                __DIR__ . '/../../stubs/config/sentinel.php' => config_path('sentinel.php'),
            ], 'sentinel-config');

            $this->publishes([
                __DIR__ . '/../../stubs/config/rules.php' => config_path('rules.php'),
            ], 'rules-config');

            $this->publishes([
                __DIR__ . '/../../stubs/app/Actions/Auth/AuthenticateUser.php' => app_path('Actions/Auth/AuthenticateUser.php'),
                __DIR__ . '/../../stubs/app/Actions/Auth/CreateNewUser.php' => app_path('Actions/Auth/CreateNewUser.php'),
                __DIR__ . '/../../stubs/app/Actions/Auth/DeleteUser.php' => app_path('Actions/Auth/DeleteUser.php'),
                __DIR__ . '/../../stubs/app/Actions/Auth/ResetUserPassword.php' => app_path('Actions/Auth/ResetUserPassword.php'),
                __DIR__ . '/../../stubs/app/Actions/Auth/UpdateUserPassword.php' => app_path('Actions/Auth/UpdateUserPassword.php'),
                __DIR__ . '/../../stubs/app/Actions/Auth/UpdateUserProfile.php' => app_path('Actions/Auth/UpdateUserProfile.php'),
                __DIR__ . '/../../stubs/app/Actions/Auth/Traits/PasswordUpdater.php' => app_path('Actions/Auth/Traits/PasswordUpdater.php'),
                __DIR__ . '/../../stubs/app/Providers/SentinelServiceProvider.php' => app_path('Providers/SentinelServiceProvider.php'),
                __DIR__ . '/../../stubs/app/Providers/AuthServiceProvider.php' => app_path('Providers/AuthServiceProvider.php'),
                __DIR__ . '/../../stubs/app/Policies/UserPolicy.php' => app_path('Policies/UserPolicy.php'),
                __DIR__ . '/../../stubs/app/Models/User.php' => app_path('Models/User.php'),
            ], 'sentinel-support');

            $this->publishes([
                __DIR__ . '/../../database/migrations/2014_10_12_000000_create_users_table.php' => database_path('migrations/2014_10_12_000000_create_users_table.php'),
            ], 'sentinel-migrations');
        }
    }

    /**
     * Configure the commands offered by the application.
     *
     * @return void
     */
    protected function configureCommands()
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->commands([
            InstallCommand::class,
            MakeResponseCommand::class,
        ]);
    }

    /**
     * Configure the routes offered by the application.
     *
     * @return void
     */
    protected function configureRoutes(): void
    {
        Route::group([
            'namespace' => 'Sentinel\Http\Controllers',
            'domain' => Config::domain([null]),
            'prefix' => Config::prefix(),
        ], function (): void {
            $this->loadRoutesFrom(__DIR__ . '/../../routes/routes.php');
        });
    }
}
