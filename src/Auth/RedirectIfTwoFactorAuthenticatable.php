<?php

namespace Cratespace\Sentinel\Auth;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Authenticatable;
use Cratespace\Sentinel\Models\Traits\TwoFactorAuthenticatable;
use Cratespace\Sentinel\Http\Responses\TwoFactorChallengeResponse;

class RedirectIfTwoFactorAuthenticatable extends Authenticate
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $this->validateCredentials($request);

        if ($this->twoFactorAuthenticationEnabled($user)) {
            $request->session()->put([
                'login.id' => $user->getKey(),
                'login.remember' => $request->filled('remember'),
            ]);

            return app(TwoFactorChallengeResponse::class);
        }

        return $next($request);
    }

    /**
     * Determine if two factor authentication is enabled on the given user model instance.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable|null $user
     *
     * @return bool
     */
    protected function twoFactorAuthenticationEnabled(?Authenticatable $user = null): bool
    {
        return optional($user)->two_factor_secret && in_array(
            TwoFactorAuthenticatable::class,
            class_uses_recursive($user)
        );
    }
}
