<?php

namespace Cratespace\Sentinel\Http\Requests;

class LogoutOtherBrowserSessionsRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->isAuthenticated();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return ['password' => ['password', 'string', 'required']];
    }
}
