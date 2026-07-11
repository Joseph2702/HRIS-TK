<?php

namespace App\Http\Controllers;

use App\Common\Response\ApiResponse;
use App\Http\Repository\UlasanLayananRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UlasanLayananController extends Controller
{
    public function __construct(private UlasanLayananRepository $repo) {}

    // Admin bisa lihat semua; orang_tua juga bisa lihat
    public function index(Request $request): JsonResponse
    {
        $limit = $request->integer('limit', 50);

        $overall = $this->repo->getOverallRating();
        // Global distribution
        $distribution = $this->repo->getRatingDistribution(null);

        $ulasanQuery = $this->repo->findAllWithUser($limit);

        return ApiResponse::success([
            'overall_avg_rating' => $overall['overall_avg_rating'],
            'overall_total_reviews' => $overall['overall_total_reviews'],
            'distribution' => $distribution,
            'ulasan' => $ulasanQuery,
        ], 'Data ulasan layanan berhasil diambil');
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'isi_ulasan' => ['nullable', 'string', 'max:2000'],
        ]);

        $user = $request->user();
        $idUser = $user?->id_user ?? $user?->id;

        if (! $idUser) {
            return ApiResponse::unauthorized('User tidak ditemukan');
        }

        $data = [
            'id_user' => (int) $idUser,
            'rating' => (int) $validated['rating'],
            'isi_ulasan' => $validated['isi_ulasan'] ?? null,
        ];

        $ulasan = $this->repo->upsertByUser($data);

        return ApiResponse::created($ulasan, 'Ulasan berhasil disimpan');
    }
}
