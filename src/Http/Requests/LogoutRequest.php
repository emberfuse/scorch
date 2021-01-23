<?php

namespace Citadel\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Citadel\Http\Requests\Concerns\AuthorizesRequests;

class LogoutRequest extends FormRequest
{
    use AuthorizesRequests;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->isAuthenticated();
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
