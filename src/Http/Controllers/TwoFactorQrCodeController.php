<?php

namespace Cratespace\Sentinel\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Contracts\Routing\ResponseFactory;

class TwoFactorQrCodeController extends Controller
{
    /**
     * Get the SVG element for the user's two factor authentication QR code.
     *
     * @param \Illuminate\Http\Request                      $request
     * @param \Illuminate\Contracts\Routing\ResponseFactory $response
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function __invoke(Request $request, ResponseFactory $response): Response
    {
        return $response->json(['svg' => $request->user()->twoFactorQrCodeSvg()]);
    }
}
