<?php

namespace App\Http\Controllers;

use App\Common\Response\ApiResponse;
use App\Http\Service\ArtikelService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class ArtikelController extends Controller
{
    public function __construct(private ArtikelService $service) {}

    public function index(Request $request): JsonResponse
    {
        $user = JWTAuth::parseToken()->authenticate();
        $tipe = $request->query('tipe'); // tentang_sekolah | layanan_sekolah | null (all)
        $data = $this->service->getAll($user, $tipe);

        return ApiResponse::success($data);
    }

    public function show(int $id): JsonResponse
    {
        return ApiResponse::success($this->service->getById($id));
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'judul_artikel'   => 'required|string|max:255',
            'konten_artikel'  => 'required|string',
            'status_artikel'  => 'sometimes|in:published,draft,archived',
            'tipe'            => 'sometimes|in:tentang_sekolah,layanan_sekolah',
            'gambar_artikel'  => 'sometimes|image|max:102400',   // 100MB
            'gambar_artikel_2' => 'sometimes|image|max:102400',
        ]);

        $user    = JWTAuth::parseToken()->authenticate();
        $artikel = $this->service->create(
            $user,
            $request->only(['judul_artikel', 'konten_artikel', 'status_artikel', 'tipe']),
            $request->file('gambar_artikel'),
            $request->file('gambar_artikel_2')
        );

        return ApiResponse::created($artikel, 'Artikel berhasil dibuat');
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'judul_artikel'   => 'sometimes|string|max:255',
            'konten_artikel'  => 'sometimes|string',
            'status_artikel'  => 'sometimes|in:published,draft,archived',
            'tipe'            => 'sometimes|in:tentang_sekolah,layanan_sekolah',
            'gambar_artikel'  => 'sometimes|image|max:102400',
            'gambar_artikel_2' => 'sometimes|image|max:102400',
        ]);

        $user    = JWTAuth::parseToken()->authenticate();
        $artikel = $this->service->update(
            $user, $id,
            $request->only(['judul_artikel', 'konten_artikel', 'status_artikel', 'tipe']),
            $request->file('gambar_artikel'),
            $request->file('gambar_artikel_2')
        );

        return ApiResponse::success($artikel, 'Artikel berhasil diperbarui');
    }

    public function destroy(int $id): JsonResponse
    {
        $user = JWTAuth::parseToken()->authenticate();
        $this->service->delete($user, $id);

        return ApiResponse::success(null, 'Artikel berhasil dihapus');
    }
}
