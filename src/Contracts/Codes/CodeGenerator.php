<?php

namespace Cratespace\Sentinel\Contracts\Codes;

interface CodeGenerator
{
    /**
     * Generate a new and unique code.
     *
     * @return string
     */
    public function generate(): string;
}
