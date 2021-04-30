<?php

namespace Cratespace\Sentinel\Models\Traits;

trait HasAddressInformation
{
    /**
     * List of all attributes used to store address information.
     *
     * @var array
     */
    protected $addressAttributes = [
        'line1',
        'line2',
        'city',
        'state',
        'country',
        'postal_code',
    ];

    /**
     * Determine if the given keys are included in the data array.
     *
     * @param array $data
     *
     * @return bool
     */
    protected function hasAddressInformation(array $data): bool
    {
        return collect($data)->contains(function ($value, $key): bool {
            return in_array($key, $this->addressAttributes);
        });
    }
}
