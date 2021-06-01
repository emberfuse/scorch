<?php

namespace Emberfuse\Scorch\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Emberfuse\Scorch\Actions\EnableTwoFactorAuthentication;
use Emberfuse\Scorch\Actions\DisableTwoFactorAuthentication;

class TwoFactorAuthenticationStatusController extends Controller
{
    /**
     * Enable two factor authentication for the user.
     *
     * @param \Illuminate\Http\Request                                $request
     * @param \Emberfuse\Scorch\Actions\EnableTwoFactorAuthentication $enable
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function store(Request $request, EnableTwoFactorAuthentication $enable): Response
    {
        $enable($request->user());

        return $this->response($request, 'two-factor-authentication-enabled');
    }

    /**
     * Disable two factor authentication for the user.
     *
     * @param \Illuminate\Http\Request                                 $request
     * @param \Emberfuse\Scorch\Actions\DisableTwoFactorAuthentication $disable
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function destroy(Request $request, DisableTwoFactorAuthentication $disable): Response
    {
        $disable($request->user());

        return $this->response($request, 'two-factor-authentication-disabled');
    }

    /**
     * Send appropriate response to action performed.
     *
     * @param \Illuminate\Http\Request $request
     * @param string                   $status
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function response(Request $request, string $status): Response
    {
        return $request->expectsJson()
            ? response()->json()
            : back()->with('status', $status);
    }
}
