<?php

namespace Cratespace\Sentinel\Sentinel;

class Features
{
    /**
     * Determine if the given feature is enabled.
     *
     * @param string $feature
     *
     * @return bool
     */
    public static function enabled(string $feature): bool
    {
        return in_array($feature, Config::features([[]]));
    }
}
