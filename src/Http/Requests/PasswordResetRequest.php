<?php

namespace Citadel\Http\Requests;

use Citadel\Citadel\Config;
use Citadel\Rules\PasswordRule;
use Illuminate\Foundation\Http\FormRequest;
use Citadel\Http\Requests\Concerns\AuthorizesRequests;

class PasswordResetRequest extends FormRequest
{
    use AuthorizesRequests;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->isGuest() && $this->has('token');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'token' => ['required', 'string'],
            Config::email() => ['required', 'string', 'email'],
            'password' => ['required', 'string', new PasswordRule(), 'confirmed'],
        ];
    }
}
