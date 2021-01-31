<?php

namespace Cratespace\Citadel\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Cratespace\Citadel\Http\Requests\Concerns\AuthorizesRequests;

class DeleteProfilePhotoRequest extends FormRequest
{
    use AuthorizesRequests;

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
        return [];
    }
}
