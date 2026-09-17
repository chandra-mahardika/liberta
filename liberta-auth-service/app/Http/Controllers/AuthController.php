<?php

namespace App\Http\Controllers;

use Liberta\Http\Request;
use Liberta\Http\Response;
use App\Services\TokenService;

class AuthController
{
    public function __construct(
        protected TokenService $tokenService
    ) {}

    public function validate(Request $request): Response
    {
        $token = $request->body()['token'] ?? null;

        if (!$token) {
            return Response::error('Token is required', 400);
        }

        $user = $this->tokenService->validate($token);

        return Response::json($user->toArray());
    }
}
