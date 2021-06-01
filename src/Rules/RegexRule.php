<?php

namespace Emberfuse\Scorch\Rules;

abstract class RegexRule
{
    /**
     * The value pattern to compare against.
     *
     * @var string
     */
    protected static $pattern;

    /**
     * Set a custom validation pattern to match values against.
     *
     * @param string $pattern
     *
     * @return void
     */
    public static function setPattern(string $pattern): void
    {
        static::$pattern = $pattern;
    }
}
