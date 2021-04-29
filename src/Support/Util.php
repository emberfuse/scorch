<?php

namespace Cratespace\Sentinel\Support;

use App\Models\User;
use Illuminate\Support\Str;

class Util
{
    /**
     * Get the base class name of the given key string.
     *
     * @param string $key
     *
     * @return string
     */
    public static function className(string $key): string
    {
        return ucfirst(Str::singular($key));
    }

    /**
     * Generate unique username from first name.
     *
     * @param string $name
     *
     * @return string
     */
    public static function makeUsername(string $name): string
    {
        $name = trim(preg_replace('/\s+/', '', $name));

        if (User::where('username', 'like', '%' . $name . '%')->count() !== 0) {
            return Str::studly($name . Str::random('5'));
        }

        return Str::studly($name);
    }
}
