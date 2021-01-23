<?php

namespace Citadel\Http\Controllers\Concerns;

use Illuminate\Pipeline\Pipeline;
use Illuminate\Container\Container;
use Illuminate\Contracts\Pipeline\Pipeline as PipelineContract;

trait InteractsWithContainer
{
    /**
     * Get the available container instance.
     *
     * @param string|null $abstract
     * @param array       $parameters
     *
     * @return mixed|\Illuminate\Contracts\Foundation\Application
     */
    public function app(?string $abstract = null, array $parameters = [])
    {
        if (is_null($abstract)) {
            return Container::getInstance();
        }

        return Container::getInstance()->make($abstract, $parameters);
    }

    /**
     * Create new instance of pipeline handler.
     *
     * @return \Illuminate\Contracts\Pipeline\Pipeline
     */
    public function pipeline(): PipelineContract
    {
        return new Pipeline($this->app());
    }
}
