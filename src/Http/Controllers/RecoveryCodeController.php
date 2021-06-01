<?php

namespace Emberfuse\Scorch\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\Routing\ResponseFactory;
use Emberfuse\Scorch\Actions\GenerateNewRecoveryCodes;
use Emberfuse\Scorch\Http\Responses\GenerateRecoveryCodesResponse;

class RecoveryCodeController extends Controller
{
    /**
     * Get the two factor authentication recovery codes for authenticated user.
     *
     * @param \Illuminate\Http\Request                      $request
     * @param \Illuminate\Contracts\Routing\ResponseFactory $response
     *
     * @return mixed
     */
    public function index(Request $request, ResponseFactory $response)
    {
        if (! $request->user()->two_factor_secret ||
            ! $request->user()->two_factor_recovery_codes) {
            return $response->json([], 200);
        }

        return $response->json(json_decode(decrypt(
            $request->user()->two_factor_recovery_codes
        ), true));
    }

    /**
     * Generate a fresh set of two factor authentication recovery codes.
     *
     * @param \Illuminate\Http\Request                           $request
     * @param \Emberfuse\Scorch\Actions\GenerateNewRecoveryCodes $generate
     *
     * @return mixed
     */
    public function store(Request $request, GenerateNewRecoveryCodes $generate)
    {
        $generate($request->user());

        return GenerateRecoveryCodesResponse::dispatch();
    }
}
