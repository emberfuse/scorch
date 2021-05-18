<?php

namespace Cratespace\Sentinel\Contracts\Actions;

interface CreatesNewResources
{
    /**
     * Create a new resource type.
     *
     * @param array      $data
     * @param array|null $options
     *
     * @return mixed
     */
    public function create(array $data, ?array $options = null);
}
