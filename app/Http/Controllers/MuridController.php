<?php

namespace App\Http\Controllers;

use App\Common\Response\ApiResponse;
use App\Http\Service\MuridService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class MuridController extends Controller
{
    public function __construct(private MuridService $service) {}

    public function index(): JsonResponse
    {
        $user = JWTAuth::parseToken()->authenticate();

        return ApiResponse::success($this->service->getAll($user));
    }

    public function show(int $id): JsonResponse
    {
        $user = JWTAuth::parseToken()->authenticate();

        return ApiResponse::success($this->service->getById($user, $id));
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'nama_murid' => 'required|string|max:100',
            'id_kelas' => 'required|integer|exists:kelas,id_kelas',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:laki-laki,perempuan',
            'foto_murid' => 'nullable|image|max:2048',
        ]);

        $user = JWTAuth::parseToken()->authenticate();
        $murid = $this->service->create(
            $user,
            $request->only(['nama_murid', 'id_kelas', 'tanggal_lahir', 'jenis_kelamin', 'status_murid']),
            $request->file('foto_murid')
        );

        return ApiResponse::created($murid, 'Profil murid berhasil ditambahkan');
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'nama_murid' => 'sometimes|string|max:100',
            'id_kelas' => 'sometimes|integer|exists:kelas,id_kelas',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:laki-laki,perempuan',
            'status_murid' => 'nullable|in:aktif,nonaktif',
            'foto_murid' => 'nullable|image|max:2048',
        ]);

        $user = JWTAuth::parseToken()->authenticate();
        $murid = $this->service->update(
            $user,
            $id,
            $request->only(['nama_murid', 'id_kelas', 'tanggal_lahir', 'jenis_kelamin', 'status_murid']),
            $request->file('foto_murid')
        );

        return ApiResponse::success($murid, 'Profil murid berhasil diperbarui');
    }

    public function destroy(int $id): JsonResponse
    {
        $user = JWTAuth::parseToken()->authenticate();
        $this->service->delete($user, $id);

        return ApiResponse::success(null, 'Profil murid berhasil dihapus');
    }
}
