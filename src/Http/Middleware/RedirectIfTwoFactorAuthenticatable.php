<?php

namespace Citadel\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Authenticatable;

class RedirectIfTwoFactorAuthenticatable extends Authenticate
{
    /**
     * Handle an incoming request.
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

            return app(TwoFactorChallengeResponse::class, [$request]);
        }

        return $next($request);
    }

    protected function twoFactorAuthenticationEnabled(Authenticatable $user): bool
    {
        return optional($user)->two_factor_secret &&
            in_array(TwoFactorAuthenticatable::class, class_uses_recursive($user));
    }
}
