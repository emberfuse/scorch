<?php

// phpcs:ignoreFile

namespace Emberfuse\Scorch\Providers;

use Emberfuse\Scorch\Actions\ConfirmPassword;
use Emberfuse\Scorch\Actions\ProvideTwoFactorAuthentication;
use Emberfuse\Scorch\Auth\Guard;
use Emberfuse\Scorch\Console\InstallCommand;
use Emberfuse\Scorch\Console\ResponseMakeCommand;
use Emberfuse\Scorch\Contracts\Actions\ConfirmsPasswords;
use Emberfuse\Scorch\Contracts\Actions\ProvidesTwoFactorAuthentication;
use Emberfuse\Scorch\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Emberfuse\Scorch\Scorch\Config;
use Illuminate\Auth\RequestGuard;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Contracts\Auth\Guard as AuthGuard;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class ScorchServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeAuthConfig();
        $this->mergeConfigFrom(__DIR__ . '/../../config/scorch.php', 'scorch');

        $this->registerAuthGuard();
        $this->registerInternalActions();
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
            'auth.guards.scorch' => array_merge([
                'driver' => 'scorch',
                'provider' => null,
            ], config('auth.guards.scorch', [])),
        ]);
    }

    /**
     * Register default authentication guard implementation.
     *
     * @return void
     */
    protected function registerAuthGuard(): void
    {
        app()->bind(
            StatefulGuard::class,
            fn () => Auth::guard(Config::guard(null))
        );
    }

    /**
     * Register all scorch internal action classes.
     *
     * @return void
     */
    protected function registerInternalActions(): void
    {
        app()->singleton(ConfirmsPasswords::class, ConfirmPassword::class);
        app()->singleton(
            ProvidesTwoFactorAuthentication::class,
            ProvideTwoFactorAuthentication::class
        );
    }

    /**
     * Configure the publishable resources offered by the package.
     *
     * @return void
     */
    protected function configurePublishing(): void
    {
        if (app()->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../stubs/config/scorch.php' => config_path('scorch.php'),
                __DIR__ . '/../../stubs/config/rules.php' => config_path('rules.php'),
            ], 'scorch-config');

            $this->publishes([
                __DIR__ . '/../../stubs/app/Actions/Auth/AuthAction.php' => app_path('Actions/Auth/AuthAction.php'),
                __DIR__ . '/../../stubs/app/Actions/Auth/AuthenticateUser.php' => app_path('Actions/Auth/AuthenticateUser.php'),
                __DIR__ . '/../../stubs/app/Actions/Auth/CreateNewUser.php' => app_path('Actions/Auth/CreateNewUser.php'),
                __DIR__ . '/../../stubs/app/Actions/Auth/DeleteUser.php' => app_path('Actions/Auth/DeleteUser.php'),
                __DIR__ . '/../../stubs/app/Actions/Auth/ResetUserPassword.php' => app_path('Actions/Auth/ResetUserPassword.php'),
                __DIR__ . '/../../stubs/app/Actions/Auth/UpdateUserPassword.php' => app_path('Actions/Auth/UpdateUserPassword.php'),
                __DIR__ . '/../../stubs/app/Actions/Auth/UpdateUserProfile.php' => app_path('Actions/Auth/UpdateUserProfile.php'),
                __DIR__ . '/../../stubs/app/Actions/Auth/UpdateUserAddress.php' => app_path('Actions/Auth/UpdateUserAddress.php'),
                __DIR__ . '/../../stubs/app/Actions/Auth/LogoutUser.php' => app_path('Actions/Auth/LogoutUser.php'),
                __DIR__ . '/../../stubs/app/Actions/Auth/Traits/PasswordUpdater.php' => app_path('Actions/Auth/Traits/PasswordUpdater.php'),
                __DIR__ . '/../../stubs/app/Providers/ScorchServiceProvider.php' => app_path('Providers/ScorchServiceProvider.php'),
                __DIR__ . '/../../stubs/app/Providers/AuthServiceProvider.php' => app_path('Providers/AuthServiceProvider.php'),
                __DIR__ . '/../../stubs/app/Policies/UserPolicy.php' => app_path('Policies/UserPolicy.php'),
                __DIR__ . '/../../stubs/app/Models/User.php' => app_path('Models/User.php'),
            ], 'scorch-support');

            $this->publishes([
                __DIR__ . '/../../database/migrations/2014_10_12_000000_create_users_table.php' => database_path('migrations/2014_10_12_000000_create_users_table.php'),
                __DIR__ . '/../../database/migrations/2019_12_14_000001_create_personal_access_tokens_table.php' => database_path('migrations/2019_12_14_000001_create_personal_access_tokens_table.php'),
            ], 'scorch-migrations');
        }
    }

    /**
     * Configure the commands offered by the application.
     *
     * @return void
     */
    protected function configureCommands()
    {
        if (! app()->runningInConsole()) {
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
        $kernel = app()->make(Kernel::class);

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
            'namespace' => 'Scorch\Http\Controllers',
            'domain' => Config::domain(null),
            'prefix' => Config::prefix(),
        ], function (): void {
            $this->loadRoutesFrom(__DIR__ . '/../../routes/routes.php');
        });
    }

    /**
     * Configure the Scorch authentication guard.
     *
     * @return void
     */
    protected function configureGuard(): void
    {
        Auth::resolved(function (AuthFactory $auth) {
            $auth->extend('scorch', function (
                Application $app,
                string $name,
                array $config
            ) use ($auth): AuthGuard {
                return tap($this->createGuard(
                    $auth,
                    $config
                ), function (AuthGuard $guard): void {
                    app()->refresh('request', $guard, 'setRequest');
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
            request(),
            $auth->createUserProvider($config['provider'] ?? null)
        );
    }
}
