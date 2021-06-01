<?php

namespace Emberfuse\Scorch\Http\Requests\Traits;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Emberfuse\Scorch\Rules\PasswordRule;

trait InputValidationRules
{
    /**
     * Get validation rules for specified validation category.
     *
     * @param string|array $validationCategory
     * @param array        $additionalRules
     *
     * @return array
     */
    protected function getRulesFor($validationCategory, array $additionalRules = []): array
    {
        $rules = [];

        if (is_string($validationCategory)) {
            $validationCategory = Arr::wrap($validationCategory);
        }

        foreach ($validationCategory as $category) {
            $rules = array_merge($rules, $this->getRules($category));
        }

        return array_merge($rules, $additionalRules);
    }

    /**
     * Get list of validation rules for specific category.
     *
     * @param string $category
     *
     * @return array
     */
    public function getRules(string $category): array
    {
        return Config::get("rules.{$category}", []);
    }

    /**
     * Rules to validate password input.
     *
     * @param array $overrides
     *
     * @return array
     */
    protected function passwordRules(array $overrides = []): array
    {
        return array_merge(['password' => [
            'required', new PasswordRule(), 'string', 'confirmed',
        ]], $overrides);
    }
}
