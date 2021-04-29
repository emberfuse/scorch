<?php

namespace Cratespace\Sentinel\Contracts\Actions;

interface CreatesNewResource
{
    /**
     * Create a new resource type.
     *
     * @param array $data
     *
     * @return mixed
     */
    public function create(array $data);
}
