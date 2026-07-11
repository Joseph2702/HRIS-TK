<?php

namespace App\Http\Repository;

use App\Domain\Entity\UlasanLayanan;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class UlasanLayananRepository
{
    public function upsertByUser(array $data): UlasanLayanan
    {
        // Unique constraint sekarang: (id_user) -> 1 ulasan global per orang tua
        return DB::transaction(function () use ($data) {
            $idUser = (int) $data['id_user'];

            $existing = UlasanLayanan::where('id_user', $idUser)->first();

            if ($existing) {
                $existing->rating = (int) ($data['rating'] ?? $existing->rating);
                $existing->isi_ulasan = $data['isi_ulasan'] ?? $existing->isi_ulasan;
                $existing->save();

                return $existing;
            }

            return UlasanLayanan::create([
                'id_artikel' => null,
                'id_user' => $idUser,
                'rating' => (int) $data['rating'],
                'isi_ulasan' => $data['isi_ulasan'] ?? null,
            ]);
        });
    }

    public function findAllWithUser(?int $limit = null): Collection
    {
        $query = UlasanLayanan::with('user')
            ->orderBy('created_at', 'desc');

        if ($limit !== null) {
            $query->limit($limit);
        }

        return $query->get();
    }

    public function findByArtikelWithUser(int $artikelId): Collection
    {
        return UlasanLayanan::with('user')
            ->where('id_artikel', $artikelId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getOverallRating(): array
    {
        $agg = UlasanLayanan::query()
            ->selectRaw('AVG(rating) as avg_rating, COUNT(*) as total_reviews')
            ->first();

        return [
            'overall_avg_rating' => $agg?->avg_rating !== null ? (float) $agg->avg_rating : 0.0,
            'overall_total_reviews' => (int) ($agg?->total_reviews ?? 0),
        ];
    }

    public function getRatingDistribution(?int $artikelId = null): array
    {
        $query = UlasanLayanan::query();

        if ($artikelId !== null) {
            $query->where('id_artikel', $artikelId);
        }

        $rows = $query->select('rating', DB::raw('COUNT(*) as total'))
            ->groupBy('rating')
            ->get();

        $map = [];
        foreach ($rows as $row) {
            $map[(int) $row->rating] = (int) $row->total;
        }

        return [
            1 => $map[1] ?? 0,
            2 => $map[2] ?? 0,
            3 => $map[3] ?? 0,
            4 => $map[4] ?? 0,
            5 => $map[5] ?? 0,
        ];
    }
}
