<?php

namespace App\Http\Controllers;

use App\Common\Response\ApiResponse;
use App\Http\Service\KelasService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function __construct(private KelasService $service) {}

    public function index(): JsonResponse
    {
        return ApiResponse::success($this->service->getAll());
    }

    public function show(int $id): JsonResponse
    {
        return ApiResponse::success($this->service->getById($id));
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:100',
            'kapasitas'  => 'nullable|integer|min:1|max:100',
        ]);

        $kelas = $this->service->create($request->only(['nama_kelas', 'kapasitas']));

        return ApiResponse::created($kelas, 'Kelas berhasil ditambahkan');
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'nama_kelas' => 'sometimes|string|max:100',
            'kapasitas'  => 'nullable|integer|min:1|max:100',
        ]);

        $kelas = $this->service->update($id, $request->only(['nama_kelas', 'kapasitas']));

        return ApiResponse::success($kelas, 'Kelas berhasil diperbarui');
    }

    public function destroy(int $id): JsonResponse
    {
        $this->service->delete($id);

        return ApiResponse::success(null, 'Kelas berhasil dihapus');
    }

    public function jadwal(int $id): JsonResponse
    {
        $jadwal = $this->service->getJadwal($id);

        return ApiResponse::success($jadwal);
    }

    public function tambahJadwal(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'id_guru' => 'required|integer|exists:guru,id_guru',
            'tanggal' => 'required|date',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'topik' => 'nullable|string|max:255',
        ]);

        $jadwal = $this->service->tambahJadwal($id, $request->only(['id_guru', 'tanggal', 'jam_mulai', 'jam_selesai', 'topik']));

        return ApiResponse::created($jadwal, 'Jadwal berhasil ditambahkan');
    }
}
