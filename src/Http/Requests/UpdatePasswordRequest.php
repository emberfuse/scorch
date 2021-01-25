<?php

namespace Cratespace\Citadel\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Cratespace\Citadel\Http\Requests\Traits\HasCustomValidator;
use Cratespace\Citadel\Http\Requests\Concerns\AuthorizesRequests;
use Cratespace\Citadel\Http\Requests\Traits\InputValidationRules;

class UpdatePasswordRequest extends FormRequest
{
    use AuthorizesRequests;
    use InputValidationRules;
    use HasCustomValidator;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->isAllowed('manage', $this->user());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return $this->getRulesFor('update_password');
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->afterValidation($this->validatePassword('current_password'));

        $this->setErrorBag('updatePassword');
    }
}
