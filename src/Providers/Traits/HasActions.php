<?php

namespace Cratespace\Sentinel\Providers\Traits;

trait HasActions
{
    /**
     * Register all action classes in the given array.
     *
     * @return void
     */
    public function registerActions(): void
    {
        if (! property_exists($this, 'actions')) {
            return;
        }

        collect($this->actions)->each(
            function (string $concrete, string $abstract): void {
                $this->app->singleton($abstract, $concrete);
            }
        );
    }
}
