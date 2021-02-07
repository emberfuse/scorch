<?php

namespace Cratespace\Sentinel\Sentinel;

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
     * Create new instance of sentinel configuration repository.
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
     * Get all sentinel configurations.
     *
     * @return array
     */
    public function all(): array
    {
        return $this->config->get('sentinel');
    }

    /**
     * Get specified sentinel configuration.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return void
     */
    public function get(string $key, $default = null)
    {
        return $this->config->get("sentinel.{$key}", $default);
    }

    /**
     * Get specified sentinel configuration dynamically.
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
