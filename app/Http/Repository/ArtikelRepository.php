<?php

namespace App\Http\Repository;

use App\Domain\Entity\Artikel;
use App\Domain\Enums\StatusArtikel;
use Illuminate\Pagination\LengthAwarePaginator;

class ArtikelRepository
{
    public function findAll(int $perPage = 10, ?string $tipe = null): LengthAwarePaginator
    {
        $q = Artikel::with('pembuat')->orderByDesc('created_at');
        if ($tipe) $q->where('tipe', $tipe);
        return $q->paginate($perPage);
    }

    public function findPublished(int $perPage = 10, ?string $tipe = null): LengthAwarePaginator
    {
        $q = Artikel::with('pembuat')
            ->where('status_artikel', StatusArtikel::PUBLISHED->value)
            ->orderByDesc('tanggal_publish');
        if ($tipe) $q->where('tipe', $tipe);
        return $q->paginate($perPage);
    }

    public function findById(int $id): ?Artikel
    {
        return Artikel::with('pembuat')->find($id);
    }

    public function create(array $data): Artikel
    {
        return Artikel::create($data);
    }

    public function update(Artikel $artikel, array $data): Artikel
    {
        $artikel->update($data);
        return $artikel->fresh('pembuat');
    }

    public function delete(Artikel $artikel): void
    {
        $artikel->delete();
    }
}
