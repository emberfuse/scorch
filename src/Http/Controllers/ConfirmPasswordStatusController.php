<?php

namespace Emberfuse\Scorch\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\Routing\ResponseFactory;

class ConfirmPasswordStatusController extends Controller
{
    /**
     * Get the password confirmation status.
     *
     * @param \Illuminate\Http\Request                      $request
     * @param \Illuminate\Contracts\Routing\ResponseFactory $response
     *
     * @return mixed
     */
    public function __invoke(Request $request, ResponseFactory $response)
    {
        return $response->json([
            'confirmed' => (time() - $request->session()->get('auth.password_confirmed_at', 0)) < $request->input('seconds', config('auth.password_timeout', 900)),
        ]);
    }
}
