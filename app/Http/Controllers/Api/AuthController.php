<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function __construct(private AuthService $authService)
    {
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->attemptLogin(
            $request->validated('email'),
            $request->validated('password')
        );

        if (!$result['user']) {
            return response()->json([
                'message' => 'Email atau password salah.',
            ], 401);
        }

        return response()->json([
            'token' => $result['token'],
            'user' => UserResource::make($result['user']),
        ]);
    }

    public function googleRedirect()
    {
        return $this->authService->redirectToGoogle();
    }

    public function googleCallback(): JsonResponse
    {
        $result = $this->authService->handleGoogleCallback();

        return response()->json([
            'token' => $result['token'],
            'user' => UserResource::make($result['user']),
        ]);
    }

    public function me(): JsonResponse
    {
        return response()->json([
            'user' => UserResource::make(request()->user()),
        ]);
    }
}
