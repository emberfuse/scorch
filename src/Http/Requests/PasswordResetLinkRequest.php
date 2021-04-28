<?php

namespace Cratespace\Sentinel\Http\Requests;

use Cratespace\Sentinel\Sentinel\Config;

class PasswordResetLinkRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->isGuest();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [Config::email() => ['required', 'string', 'email']];
    }
}
