<?php

namespace Emberfuse\Scorch\Http\Requests\Traits;

use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Validation\Factory;
use Emberfuse\Scorch\Support\Concerns\InteractsWithContainer;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;

trait HasCustomValidator
{
    use InteractsWithContainer;

    /**
     * Validator after hook functions.
     *
     * @var \Closure
     */
    protected $afterValidationHook;

    /**
     * The custom key to be used for the view error bag.
     *
     * @var string
     */
    protected $customErrorBag;

    /**
     * Get request validator instance.
     *
     * @param \Illuminate\Contracts\Validation\Factory
     *
     * @return ValidatorContract
     */
    public function validator(Factory $factory): ValidatorContract
    {
        $validator = $this->makeValidator($factory);

        if (! is_null($this->afterValidationHook)) {
            $validator = $this->addAfterValidationHook($validator);
        }

        if (! is_null($this->customErrorBag)) {
            $validator = $this->addCustomErrorBag($validator);
        }

        return $validator;
    }

    /**
     * Make new instance of validator.
     *
     * @param \Illuminate\Contracts\Validation\Factory
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function makeValidator(Factory $factory): ValidatorContract
    {
        return $factory->make(
            $this->validationData(),
            $this->rules(),
            $this->messages(),
            $this->attributes()
        );
    }

    /**
     * Attach callbacks to be run after validation is completed.
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function addAfterValidationHook(ValidatorContract $validator): ValidatorContract
    {
        $validator->after($this->afterValidationHook);

        return $validator;
    }

    /**
     * Set callbacks to be run after validation is completed.
     *
     * @param \Closure $callback
     *
     * @return \Illuminate\Foundation\Http\FormRequest
     */
    public function afterValidation(Closure $callback): FormRequest
    {
        $this->afterValidationHook = $callback;

        return $this;
    }

    /**
     * Add custom error message bag.
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function addCustomErrorBag(ValidatorContract $validator): ValidatorContract
    {
        $validator->validateWithBag($this->customErrorBag);

        return $validator;
    }

    /**
     * Specify custom error message bag.
     *
     * @param string $callback
     *
     * @return \Illuminate\Foundation\Http\FormRequest
     */
    public function setErrorBag(string $bag): FormRequest
    {
        $this->customErrorBag = $bag;

        return $this;
    }

    /**
     * Set custom validator function to validate user input password.
     *
     * @param string $inputName
     *
     * @return \Closure
     */
    protected function validatePassword(string $inputName = 'password'): Closure
    {
        return function (ValidatorContract $validator) use ($inputName) {
            if (! $this->validateUserCredentials(['password' => $this->{$inputName}])) {
                $validator->errors()->add(
                    $inputName,
                    __('The provided password does not match your current password.')
                );
            }
        };
    }

    /**
     * Validate currently authenticated user's credentials.
     *
     * @param array $credentials
     *
     * @return bool
     */
    protected function validateUserCredentials(array $credentials): bool
    {
        return $this->resolve(StatefulGuard::class)
            ->getProvider()
            ->validateCredentials($this->user(), $credentials);
    }
}
