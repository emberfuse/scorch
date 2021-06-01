<?php

namespace Emberfuse\Scorch\Http\Requests;

use Emberfuse\Scorch\Scorch\Config;
use Emberfuse\Scorch\Rules\PasswordRule;

class PasswordResetRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->isGuest() && $this->has('token');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'token' => ['required', 'string'],
            Config::email() => ['required', 'string', 'email'],
            'password' => ['required', 'string', new PasswordRule(), 'confirmed'],
        ];
    }
}
