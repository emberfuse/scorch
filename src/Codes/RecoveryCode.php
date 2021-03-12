<?php

namespace Cratespace\Sentinel\Codes;

use Illuminate\Support\Str;
use Cratespace\Sentinel\Contracts\Codes\CodeGenerator;

class RecoveryCode extends Code implements CodeGenerator
{
    /**
     * Generate a new and unique code.
     *
     * @return string
     */
    public function generate(): string
    {
        return Str::random(10) . '-' . Str::random(10);
    }
}
