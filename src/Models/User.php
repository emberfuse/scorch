<?php

namespace Citadel\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /**
     * Get all user attributes.
     *
     * @return array
     */
    public static function getAllAttributes(): array
    {
        $attributes = $this->getAttributes();

        foreach ($this->getFillable() as $column) {
            if (! array_key_exists($column, $attributes)) {
                $attributes[$column] = null;
            }
        }

        return $attributes;
    }
}
