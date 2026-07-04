<?php

namespace App\Http\Repository;

use App\Domain\Entity\Kelas;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class KelasRepository
{
    public function findAll(int $perPage = 15): LengthAwarePaginator
    {
        return Kelas::withCount('murid')->paginate($perPage);
    }

    public function findAllAsList(): Collection
    {
        return Kelas::all();
    }

    public function findById(int $id): ?Kelas
    {
        return Kelas::with(['murid', 'jadwalKelas'])->find($id);
    }

    public function create(array $data): Kelas
    {
        return Kelas::create($data);
    }

    public function update(Kelas $kelas, array $data): Kelas
    {
        $kelas->update($data);
        return $kelas->fresh();
    }

    public function delete(Kelas $kelas): void
    {
        $kelas->delete();
    }

    public function hasActiveJadwal(int $kelasId): bool
    {
        return Kelas::find($kelasId)?->jadwalKelas()->exists() ?? false;
    }
}
