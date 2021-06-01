<?php

namespace Emberfuse\Scorch\Http\Requests;

class DeleteUserRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->isAllowed('manage', $this->user(), false);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return ['password' => ['required', 'string']];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->afterValidation($this->validatePassword());

        $this->setErrorBag('deleteUser');
    }
}
