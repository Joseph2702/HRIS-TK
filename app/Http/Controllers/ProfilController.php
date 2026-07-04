<?php

namespace App\Http\Controllers;

use App\Common\Response\ApiResponse;
use App\Http\Service\ProfilService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class ProfilController extends Controller
{
    public function __construct(private ProfilService $service) {}

    public function show(): JsonResponse
    {
        $user = JWTAuth::parseToken()->authenticate();
        $data = $this->service->getProfil($user);

        return ApiResponse::success($data);
    }

    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'nama' => 'sometimes|string|max:100',
            'no_hp' => 'nullable|string|max:20',
            'jenis_kelamin' => 'nullable|in:laki-laki,perempuan',
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'password' => 'nullable|string|min:8|confirmed',
            'foto_profile' => 'nullable|image|max:2048',
            'spesialisasi' => 'nullable|string|max:100',
            'pekerjaan' => 'nullable|string|max:100',
        ]);

        $user = JWTAuth::parseToken()->authenticate();
        $data = $this->service->updateProfil($user, $request->except('foto_profile'), $request->file('foto_profile'));

        return ApiResponse::success($data, 'Profil berhasil disimpan');
    }
}
