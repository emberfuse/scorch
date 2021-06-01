<?php

namespace Emberfuse\Scorch\Http\Requests;

class VerifyEmailRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        if (! hash_equals((string) $this->route('id'), (string) $this->user()->getKey())) {
            return false;
        }

        if (! hash_equals((string) $this->route('hash'), sha1($this->user()->getEmailForVerification()))) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [];
    }
}
