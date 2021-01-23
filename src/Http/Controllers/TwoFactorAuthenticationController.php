<?php

namespace Citadel\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Citadel\Actions\EnableTwoFactorAuthentication;
use Citadel\Actions\DisableTwoFactorAuthentication;

class TwoFactorAuthenticationController extends Controller
{
    /**
     * Enable two factor authentication for the user.
     *
     * @param \Illuminate\Http\Request                       $request
     * @param \Citadel\Actions\EnableTwoFactorAuthentication $enable
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function store(Request $request, EnableTwoFactorAuthentication $enable)
    {
        $enable($request->user());

        return $this->response($request, 'two-factor-authentication-enabled');
    }

    /**
     * Disable two factor authentication for the user.
     *
     * @param \Illuminate\Http\Request                        $request
     * @param \Citadel\Actions\DisableTwoFactorAuthentication $disable
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function destroy(Request $request, DisableTwoFactorAuthentication $disable)
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
        return $request->wantsJson() ? response()->json() : back()->with('status', $status);
    }
}
