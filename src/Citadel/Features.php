<?php

namespace Citadel\Citadel;

use Citadel\Auth\Config;

class Features
{
    public function hasFeature(string $key): bool
    {
        if (! is_null($this->getFeature($key))) {
            return true;
        }

        return false;
    }

    public function getFeature(string $key)
    {
        return Config::features()[$key];
    }
}
