<?php

namespace Cratespace\Sentinel\Codes;

abstract class Code
{
    /**
     * String of acceptable characters.
     *
     * @var string
     */
    protected static $characterPool = 'ABCDEFGHJKLMNOPQRSTUVWXYZ23456789';

    /**
     * Default UID character length.
     */
    public const CHARACTER_LENGTH = 24;
}
