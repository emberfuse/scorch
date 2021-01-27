<?php

namespace Cratespace\Citadel\Citadel;

use Illuminate\Support\Str;
use Illuminate\Contracts\Config\Repository as ConfigContract;

class Config
{
    /**
     * Application configurations repository.
     *
     * @var \Illuminate\Contracts\Config\Repository
     */
    protected $config;

    /**
     * Create new instance of citadel configuration repository.
     *
     * @param \Illuminate\Contracts\Config\Repository $config
     *
     * @return void
     */
    public function __construct(ConfigContract $config)
    {
        $this->config = $config;
    }

    /**
     * Get all citadel configurations.
     *
     * @return array
     */
    public function all(): array
    {
        return $this->config->get('citadel');
    }

    /**
     * Get specified citadel configuration.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return void
     */
    public function get(string $key, $default = null)
    {
        return $this->config->get("citadel.{$key}", $default);
    }

    /**
     * Get specified citadel configuration dynamically.
     *
     * @param string $name
     * @param mixed  $arguments
     *
     * @return void
     */
    public static function __callStatic(string $name, $arguments)
    {
        return (new static(app('config')))->get(Str::snake($name), ...$arguments);
    }
}
