<?php

namespace Cratespace\Citadel\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Cratespace\Citadel\Actions\GenerateNewRecoveryCodes;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Contracts\Routing\ResponseFactory;
use Cratespace\Citadel\Http\Responses\GenerateRecoveryCodesResponse;

class RecoveryCodeController extends Controller
{
    /**
     * Get the two factor authentication recovery codes for authenticated user.
     *
     * @param \Illuminate\Http\Request                      $request
     * @param \Illuminate\Contracts\Routing\ResponseFactory $response
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, ResponseFactory $response): JsonResponse
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
     * @param \Illuminate\Http\Request                          $request
     * @param \Laravel\Fortify\Actions\GenerateNewRecoveryCodes $generate
     *
     * @return \Illuminate\Contracts\Support\Responsable
     */
    public function store(Request $request, GenerateNewRecoveryCodes $generate): Responsable
    {
        $generate($request->user());

        return $this->app(GenerateRecoveryCodesResponse::class);
    }
}
