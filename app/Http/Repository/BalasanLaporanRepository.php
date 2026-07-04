<?php

namespace App\Http\Repository;

use App\Domain\Entity\BalasanLaporan;
use Illuminate\Database\Eloquent\Collection;

class BalasanLaporanRepository
{
    public function findByLaporan(int $laporanId): Collection
    {
        return BalasanLaporan::with('user')
            ->where('id_laporan', $laporanId)
            ->orderBy('created_at')
            ->get();
    }

    public function create(array $data): BalasanLaporan
    {
        return BalasanLaporan::create($data);
    }

    public function delete(BalasanLaporan $balasan): void
    {
        $balasan->delete();
    }

    public function findById(int $id): ?BalasanLaporan
    {
        return BalasanLaporan::with(['laporan', 'user'])->find($id);
    }
}
