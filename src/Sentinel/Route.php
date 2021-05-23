<?php

namespace Cratespace\Sentinel\Sentinel;

use Illuminate\Support\Arr;

class Route
{
    /**
     * Determine if a given route is enabled.
     *
     * @param string $name
     *
     * @return bool
     */
    public static function isEnabled(string $name): bool
    {
        $routes = Config::authRoutes();

        if (Arr::exists($routes, $name)) {
            return $routes[$name];
        }

        return false;
    }
}
