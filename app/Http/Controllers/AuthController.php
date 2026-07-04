<?php

namespace App\Http\Controllers;

use App\Common\Response\ApiResponse;
use App\Http\Service\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function __construct(private AuthService $service) {}

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $result = $this->service->login($request->email, $request->password);

        return ApiResponse::success($result, 'Login berhasil');
    }

    public function logout(): JsonResponse
    {
        $this->service->logout();

        return ApiResponse::success(null, 'Logout berhasil');
    }

    public function refresh(): JsonResponse
    {
        $result = $this->service->refresh();

        return ApiResponse::success($result, 'Token diperbarui');
    }

    public function me(Request $request): JsonResponse
    {
        $user = JWTAuth::parseToken()->authenticate();
        $data = $this->service->me($user);

        return ApiResponse::success($data);
    }
}
