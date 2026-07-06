<?php

namespace App\Http\Controllers;

use App\Common\Response\ApiResponse;
use App\Http\Service\LaporanKegiatanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class LaporanKegiatanController extends Controller
{
    public function __construct(private LaporanKegiatanService $service) {}

    public function index(Request $request): JsonResponse
    {
        $user = JWTAuth::parseToken()->authenticate();
        $muridId = $request->query('murid_id');

        return ApiResponse::success($this->service->getAll($user, $muridId ? intval($muridId) : null));
    }

    public function show(int $id): JsonResponse
    {
        $user = JWTAuth::parseToken()->authenticate();

        return ApiResponse::success($this->service->getById($user, $id));
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'id_jadwal' => 'required|integer|exists:jadwal_kelas,id_jadwal',
            'id_murid' => 'required|integer|exists:murid,id_murid',
            'judul_laporan' => 'required|string|max:255',
            'isi_laporan' => 'nullable|string',
        ]);

        $user = JWTAuth::parseToken()->authenticate();
        $laporan = $this->service->create($user, $request->only(['id_jadwal', 'id_murid', 'judul_laporan', 'isi_laporan']));

        return ApiResponse::created($laporan, 'Laporan berhasil dibuat');
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'judul_laporan' => 'sometimes|string|max:255',
            'isi_laporan' => 'nullable|string',
        ]);

        $user = JWTAuth::parseToken()->authenticate();
        $laporan = $this->service->update($user, $id, $request->only(['judul_laporan', 'isi_laporan']));

        return ApiResponse::success($laporan, 'Laporan berhasil diperbarui');
    }

    public function destroy(int $id): JsonResponse
    {
        $user = JWTAuth::parseToken()->authenticate();
        $this->service->delete($user, $id);

        return ApiResponse::success(null, 'Laporan berhasil dihapus');
    }

    public function balas(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'isi_balasan' => 'required|string',
        ]);

        $user = JWTAuth::parseToken()->authenticate();
        $balasan = $this->service->kirimBalasan($user, $id, $request->isi_balasan);

        return ApiResponse::created($balasan, 'Balasan berhasil dikirim');
    }
}
