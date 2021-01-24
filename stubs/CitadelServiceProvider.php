<?php

namespace App\Providers;

use App\Actions\Citadel\DeleteUser;
use App\Actions\Citadel\CreateNewUser;
use Illuminate\Support\ServiceProvider;
use App\Actions\Citadel\AuthenticateUser;
use App\Actions\Citadel\ResetUserPassword;
use App\Actions\Citadel\UpdateUserProfile;
use App\Actions\Citadel\UpdateUserPassword;
use Citadel\Contracts\Actions\DeletesUsers;
use Citadel\Contracts\Actions\CreatesNewUsers;
use Citadel\Contracts\Auth\AuthenticatesUsers;
use Citadel\Contracts\Actions\ResetsUserPasswords;
use Citadel\Contracts\Actions\UpdatesUserProfiles;
use Citadel\Contracts\Actions\UpdatesUserPasswords;

class CitadelServiceProvider extends ServiceProvider
{
    /**
     * The citadel action classes.
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
