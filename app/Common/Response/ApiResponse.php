<?php

namespace App\Common\Response;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    public static function success(mixed $data = null, string $message = 'Berhasil', int $code = 200): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    public static function error(string $message = 'Terjadi kesalahan', int $code = 500, mixed $errors = null): JsonResponse
    {
        $body = ['status' => false, 'message' => $message];
        if ($errors !== null) {
            $body['errors'] = $errors;
        }

        return response()->json($body, $code);
    }

    public static function validationError(mixed $errors, string $message = 'Validasi gagal'): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'errors' => $errors,
        ], 422);
    }

    public static function notFound(string $message = 'Data tidak ditemukan'): JsonResponse
    {
        return self::error($message, 404);
    }

    public static function unauthorized(string $message = 'Akses tidak diizinkan'): JsonResponse
    {
        return self::error($message, 403);
    }

    public static function unauthenticated(string $message = 'Silakan login terlebih dahulu'): JsonResponse
    {
        return self::error($message, 401);
    }

    public static function created(mixed $data = null, string $message = 'Berhasil dibuat'): JsonResponse
    {
        return self::success($data, $message, 201);
    }
}
