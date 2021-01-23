<?php

namespace Citadel\Auth;

use Illuminate\Contracts\Auth\Authenticatable;
use Citadel\Http\Responses\TwoFactorChallengeResponse;

class RedirectIfTwoFactorAuthenticatable extends Authenticate
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param callable                 $next
     *
     * @return mixed
     */
    public function handle($request, $next)
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
