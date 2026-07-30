<?php

namespace App\Http\Controllers;

use App\Common\Response\ApiResponse;
use App\Http\Repository\BalasanLaporanRepository;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Facades\JWTAuth;

class BalasanLaporanController extends Controller
{
    public function __construct(private BalasanLaporanRepository $repo) {}

    public function destroy(int $id): JsonResponse
    {
        $user = JWTAuth::parseToken()->authenticate();

        $balasan = $this->repo->findById($id);
        if (! $balasan) {
            return ApiResponse::notFound('Balasan tidak ditemukan');
        }

        // Admin can delete any balasan
        if ($user->hasRole('admin')) {
            $this->repo->delete($balasan);
            return ApiResponse::success(null, 'Balasan berhasil dihapus');
        }

        // Guru can delete any balasan
        if ($user->hasRole('guru')) {
            $this->repo->delete($balasan);
            return ApiResponse::success(null, 'Balasan berhasil dihapus');
        }

        // Orang tua can only delete their own balasan within 24 hours
        if ($user->hasRole('orang_tua')) {
            if ($balasan->id_user !== $user->id_user) {
                return ApiResponse::unauthorized('Anda hanya dapat menghapus komentar Anda sendiri');
            }

            if (! $balasan->created_at) {
                return ApiResponse::error('Data komentar tidak valid', 400);
            }

            $now = now();
            $diffInHours = $balasan->created_at->diffInHours($now);

            if ($diffInHours >= 24) {
                return ApiResponse::unauthorized('Komentar hanya dapat dihapus dalam waktu 24 jam setelah dibuat');
            }

            $this->repo->delete($balasan);
            return ApiResponse::success(null, 'Balasan berhasil dihapus');
        }

        return ApiResponse::unauthorized('Akses tidak diizinkan');
    }
}
