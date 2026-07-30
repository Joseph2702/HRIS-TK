<?php

namespace App\Http\Repository;

use App\Domain\Entity\Murid;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class MuridRepository
{
    public function findAll(): Collection
    {
        return Murid::query()->orderByDesc('created_at')->get();
    }

    public function findById(string $id): ?Murid
    {
        return Murid::query()->where('id_murid', $id)->first();
    }

    public function findByKelas(int $kelasId): Collection
    {
        return Murid::query()
            ->where('id_kelas', $kelasId)
            ->orderBy('nama_murid')
            ->get();
    }

    public function findByOrangTua(int $orangTuaId): Collection
    {
        return Murid::query()->where('id_orang_tua', $orangTuaId)->get();
    }

    public function findFirstActiveByOrangTuaId(int $orangTuaId): ?Murid
    {
        return Murid::query()
            ->where('id_orang_tua', $orangTuaId)
            ->where('status_murid', 'aktif')
            ->orderByDesc('created_at')
            ->first();
    }

    public function getNextCounterForYear(string $year): int
    {
        $last = DB::table('murid')
            ->where('id_murid', 'like', $year . '-%')
            ->orderBy('id_murid', 'desc')
            ->value('id_murid');

        if (! $last) {
            return 1;
        }

        $parts = explode('-', $last);
        $counter = isset($parts[1]) ? (int) $parts[1] : 0;

        return $counter + 1;
    }
}

