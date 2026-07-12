<?php

namespace App\Http\Repository;

use App\Domain\Entity\Murid;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class MuridRepository
{
    public function findById(int $id): ?Murid
    {
        return Murid::query()->where('id_murid', $id)->first();
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
}

