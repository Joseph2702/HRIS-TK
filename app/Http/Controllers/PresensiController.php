<?php

namespace App\Http\Controllers;

use App\Common\Response\ApiResponse;
use App\Http\Service\PresensiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class PresensiController extends Controller
{
    public function __construct(private PresensiService $service) {}

    public function byJadwal(int $jadwalId): JsonResponse
    {
        $data = $this->service->getByJadwal($jadwalId);

        return ApiResponse::success($data);
    }

    public function simpan(Request $request, int $jadwalId): JsonResponse
    {
        $request->validate([
            'presensi' => 'required|array|min:1',
            'presensi.*.id_murid' => 'required|integer|exists:murid,id_murid',
            'presensi.*.status_kehadiran' => 'required|in:hadir,tidak_hadir,izin,sakit',
            'presensi.*.keterangan' => 'nullable|string',
        ]);

        $user = JWTAuth::parseToken()->authenticate();
        $this->service->simpanPresensi($user, $jadwalId, $request->presensi);

        return ApiResponse::success(null, 'Presensi berhasil disimpan');
    }

    public function ubahStatus(Request $request, int $presensiId): JsonResponse
    {
        $request->validate([
            'status_kehadiran' => 'required|in:hadir,tidak_hadir,izin,sakit',
            'keterangan' => 'nullable|string',
        ]);

        $user = JWTAuth::parseToken()->authenticate();
        $presensi = $this->service->ubahStatus($user, $presensiId, $request->status_kehadiran, $request->keterangan);

        return ApiResponse::success($presensi, 'Status presensi berhasil diubah');
    }

    public function history(Request $request, int $kelasId): JsonResponse
    {
        $data = $this->service->historyByKelas($kelasId);

        return ApiResponse::success($data);
    }

    public function byMurid(int $muridId): JsonResponse
    {
        $data = $this->service->getByMurid($muridId);

        return ApiResponse::success($data);
    }
}
