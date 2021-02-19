<?php

namespace Cratespace\Sentinel\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Exceptions\HttpResponseException;
use Laravel\Fortify\Http\Responses\FailedTwoFactorLoginResponse;
use Cratespace\Sentinel\Http\Requests\Concerns\AuthorizesRequests;
use Cratespace\Sentinel\Contracts\Actions\ProvidesTwoFactorAuthentication;

class TwoFactorLoginRequest extends FormRequest
{
    use AuthorizesRequests;

    /**
     * The user attempting the two factor challenge.
     *
     * @var mixed
     */
    protected $challengedUser;

    /**
     * Indicates if the user wished to be remembered after login.
     *
     * @var bool
     */
    protected $remember;

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
            'code' => ['nullable', 'string'],
            'recovery_code' => ['nullable', 'string'],
        ];
    }

    /**
     * Determine if the request has a valid two factor code.
     *
     * @return bool
     */
    public function hasValidCode(): bool
    {
        return $this->code && app(ProvidesTwoFactorAuthentication::class)->verify(
            decrypt($this->challengedUser()->two_factor_secret),
            $this->code
        );
    }

    /**
     * Get the valid recovery code if one exists on the request.
     *
     * @return string|null
     */
    public function validRecoveryCode(): ?string
    {
        if (! $this->recovery_code) {
            return null;
        }

        return collect($this->challengedUser()->recoveryCodes())
            ->first(function (string $code): ?string {
                return hash_equals($this->recovery_code, $code) ? $code : null;
            });
    }

    /**
     * Get the user that is attempting the two factor challenge.
     *
     * @return mixed
     */
    public function challengedUser()
    {
        if ($this->challengedUser) {
            return $this->challengedUser;
        }

        $model = app(StatefulGuard::class)->getProvider()->getModel();

        if (! $this->session()->has('login.id') ||
            ! $user = $model::find($this->session()->pull('login.id'))) {
            throw new HttpResponseException(app(FailedTwoFactorLoginResponse::class)->toResponse($this));
        }

        return $this->challengedUser = $user;
    }

    /**
     * Determine if the user wanted to be remembered after login.
     *
     * @return bool
     */
    public function remember(): bool
    {
        if (! $this->remember) {
            $this->remember = $this->session()->pull('login.remember', false);
        }

        return $this->remember;
    }
}
