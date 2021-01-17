<?php

namespace Citadel\Providers;

use Citadel\Auth\Config;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class CitadelServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/citadel.php', 'citadel');

        $this->registerResponseBindings();

        $this->registerAuthGuard();
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
}
