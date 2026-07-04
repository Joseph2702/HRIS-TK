<?php

namespace App\Http\Controllers;

use App\Common\Response\ApiResponse;
use App\Http\Repository\BalasanLaporanRepository;
use Illuminate\Http\JsonResponse;

class BalasanLaporanController extends Controller
{
    public function __construct(private BalasanLaporanRepository $repo) {}

    public function destroy(int $id): JsonResponse
    {
        $balasan = $this->repo->findById($id);
        if (! $balasan) {
            return ApiResponse::notFound('Balasan tidak ditemukan');
        }

        $this->repo->delete($balasan);

        return ApiResponse::success(null, 'Balasan berhasil dihapus');
    }
}
