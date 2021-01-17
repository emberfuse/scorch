<?php

namespace Citadel\Auth;

class RecoveryCode
{
    public static function generate(): string
    {
        return 'secret-code';
    }
}
