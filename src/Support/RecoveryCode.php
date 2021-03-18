<?php

namespace Cratespace\Sentinel\Support;

use Illuminate\Support\Str;

class RecoveryCode
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
