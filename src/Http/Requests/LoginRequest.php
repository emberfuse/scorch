<?php

namespace Citadel\Http\Requests;

use Citadel\Auth\Config;
use Illuminate\Foundation\Http\FormRequest;
use Citadel\Http\Requests\Concerns\AuthorizesRequests;

class LoginRequest extends FormRequest
{
    use AuthorizesRequests;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->isGuest();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            Config::username() => ['required', 'string'],
            'password' => ['required', 'string'],
            'remember' => ['nullable']
        ];
    }
}
