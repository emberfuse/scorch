<?php

namespace Cratespace\Sentinel\Providers;

use Illuminate\Auth\RequestGuard;
use Cratespace\Sentinel\Auth\Guard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Cratespace\Sentinel\Sentinel\Config;
use Illuminate\Contracts\Auth\StatefulGuard;
use Cratespace\Sentinel\Console\InstallCommand;
use Cratespace\Sentinel\Actions\ConfirmPassword;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Auth\Guard as AuthGuard;
use Cratespace\Sentinel\Console\ResponseMakeCommand;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Cratespace\Sentinel\Contracts\Actions\ConfirmsPasswords;
use Cratespace\Sentinel\Actions\ProvideTwoFactorAuthentication;
use Cratespace\Sentinel\Contracts\Actions\ProvidesTwoFactorAuthentication;
use Cratespace\Sentinel\Http\Middleware\EnsureFrontendRequestsAreStateful;

class SentinelServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeAuthConfig();
        $this->mergeConfigFrom(__DIR__ . '/../../config/sentinel.php', 'sentinel');

        $this->registerAuthGuard();
        $this->registerInternalActions();
        $this->registerInternalConfig();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->configurePublishing();
        $this->configureCommands();
        $this->configureMiddleware();
        $this->configureRoutes();
        $this->configureGuard();
    }

    /**
     * Merge API auth guard configurations.
     *
     * @return void
     */
    protected function mergeAuthConfig(): void
    {
        config([
            'auth.guards.sentinel' => array_merge([
                'driver' => 'sentinel',
                'provider' => null,
            ], config('auth.guards.sentinel', [])),
        ]);
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
            fn () => Auth::guard(Config::guard(['sentinel']))
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
        $this->app->singleton(
            ProvidesTwoFactorAuthentication::class,
            ProvideTwoFactorAuthentication::class
        );
    }

    /**
     * Register Sentinel config class.
     *
     * @return void
     */
    protected function registerInternalConfig(): void
    {
        $this->app->singleton(Config::class, function (Application $app): Config {
            return new Config($app['config']);
        });
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
                __DIR__ . '/../../stubs/config/rules.php' => config_path('rules.php'),
            ], 'sentinel-config');

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
                __DIR__ . '/../../database/migrations/2019_12_14_000001_create_personal_access_tokens_table.php' => database_path('migrations/2019_12_14_000001_create_personal_access_tokens_table.php'),
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
            ResponseMakeCommand::class,
        ]);
    }

    /**
     * Configure the Sanctum middleware and priority.
     *
     * @return void
     */
    protected function configureMiddleware(): void
    {
        $kernel = $this->app->make(Kernel::class);

        $kernel->prependToMiddlewarePriority(
            EnsureFrontendRequestsAreStateful::class
        );
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

    /**
     * Configure the Sentinel authentication guard.
     *
     * @return void
     */
    protected function configureGuard(): void
    {
        Auth::resolved(function (AuthFactory $auth) {
            $auth->extend('sentinel', function (Application $app, string $name, array $config) use ($auth): AuthGuard {
                return tap($this->createGuard($auth, $config), function (AuthGuard $guard): void {
                    $this->app->refresh('request', $guard, 'setRequest');
                });
            });
        });
    }

    /**
     * Register the guard.
     *
     * @param \Illuminate\Contracts\Auth\Factory $auth
     * @param array                              $config
     *
     * @return \Illuminate\Auth\RequestGuard
     */
    protected function createGuard(AuthFactory $auth, array $config): RequestGuard
    {
        return new RequestGuard(
            new Guard($auth, Config::expiration(), $config['provider']),
            $this->app['request'],
            $auth->createUserProvider($config['provider'] ?? null)
        );
    }
}
