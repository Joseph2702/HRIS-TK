<?php

namespace App\Http\Repository;

use App\Domain\Entity\Murid;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class MuridRepository
{
    public function findAll(int $perPage = 15): LengthAwarePaginator
    {
        return Murid::with(['kelas', 'orangTua.user'])->paginate($perPage);
    }

    public function findById(int $id): ?Murid
    {
        return Murid::with(['kelas', 'orangTua.user'])->find($id);
    }

    public function findByOrangTua(int $orangTuaId): Collection
    {
        return Murid::with(['kelas'])
            ->where('id_orang_tua', $orangTuaId)
            ->get();
    }

    public function findByKelas(int $kelasId): Collection
    {
        return Murid::with(['orangTua.user'])
            ->where('id_kelas', $kelasId)
            ->where('status_murid', 'aktif')
            ->get();
    }

    public function create(array $data): Murid
    {
        return Murid::create($data);
    }

    public function update(Murid $murid, array $data): Murid
    {
        $murid->update($data);
        return $murid->fresh(['kelas', 'orangTua.user']);
    }

    public function delete(Murid $murid): void
    {
        $murid->delete();
    }
}
