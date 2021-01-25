<?php

namespace Cratespace\Citadel\Codes;

use Illuminate\Support\Str;

class RecoveryCode extends Code
{
    /**
     * Generate a new and unique code.
     *
     * @return string
     */
    public static function generate(): string
    {
        return Str::random(10) . '-' . Str::random(10);
    }
}
