<?php

namespace App\Providers;

use App\Actions\Auth\DeleteUser;
use App\Actions\Auth\CreateNewUser;
use App\Actions\Auth\AuthenticateUser;
use App\Actions\Auth\ResetUserPassword;
use App\Actions\Auth\UpdateUserProfile;
use Illuminate\Support\ServiceProvider;
use App\Actions\Auth\UpdateUserPassword;
use Cratespace\Sentinel\Contracts\Actions\DeletesUsers;
use Cratespace\Sentinel\Contracts\Actions\CreatesNewUsers;
use Cratespace\Sentinel\Contracts\Actions\AuthenticatesUsers;
use Cratespace\Sentinel\Contracts\Actions\ResetsUserPasswords;
use Cratespace\Sentinel\Contracts\Actions\UpdatesUserProfiles;
use Cratespace\Sentinel\Contracts\Actions\UpdatesUserPasswords;

class SentinelServiceProvider extends ServiceProvider
{
    /**
     * The sentinel action classes.
     *
     * @var array
     */
    protected static $actions = [
        AuthenticatesUsers::class => AuthenticateUser::class,
        CreatesNewUsers::class => CreateNewUser::class,
        ResetsUserPasswords::class => ResetUserPassword::class,
        UpdatesUserPasswords::class => UpdateUserPassword::class,
        UpdatesUserProfiles::class => UpdateUserProfile::class,
        DeletesUsers::class => DeleteUser::class,
    ];

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
     * Register all sentinel action classes to the service container.
     *
     * @return void
     */
    protected function registerActions(): void
    {
        collect(static::$actions)->each(
            fn ($concrete, $abstract) => $this->app->singleton($abstract, $concrete)
        );
    }
}
