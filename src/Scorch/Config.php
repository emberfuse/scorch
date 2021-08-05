<?php

namespace Emberfuse\Scorch\Scorch;

use Illuminate\Support\Facades\Config as AppConfig;
use Illuminate\Support\Str;

class Config
{
    /**
     * Get all scorch configurations.
     *
     * @return array
     */
    public static function all(): array
    {
        return AppConfig::get('scorch');
    }

    /**
     * Get specified scorch configuration.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        return AppConfig::get("scorch.{$key}", $default);
    }

    /**
     * Get specified scorch configuration dynamically.
     *
     * @param string $name
     * @param mixed  $arguments
     *
     * @return mixed
     */
    public static function __callStatic(string $name, $arguments)
    {
        return static::get(Str::snake($name), ...$arguments);
    }
}
