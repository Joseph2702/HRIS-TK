<?php

namespace App\Http\Repository;

use App\Domain\Entity\Presensi;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class PresensiRepository
{
    public function findByJadwal(int $jadwalId): Collection
    {
        return Presensi::with(['murid', 'pencatat'])
            ->where('id_jadwal', $jadwalId)
            ->get();
    }

    public function findByMurid(string $muridId, int $perPage = 15): LengthAwarePaginator
    {
        return Presensi::with(['jadwal.kelas', 'jadwal.guru.user'])
            ->where('id_murid', $muridId)
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function upsert(int $jadwalId, string $muridId, array $data): Presensi
    {
        return Presensi::updateOrCreate(
            ['id_jadwal' => $jadwalId, 'id_murid' => $muridId],
            $data
        );
    }

    public function findOne(int $jadwalId, string $muridId): ?Presensi
    {
        return Presensi::where('id_jadwal', $jadwalId)
            ->where('id_murid', $muridId)
            ->first();
    }

    public function findById(int $id): ?Presensi
    {
        return Presensi::with(['murid', 'jadwal.kelas', 'pencatat'])->find($id);
    }

    public function historyByKelas(int $kelasId, int $perPage = 15): LengthAwarePaginator
    {
        return Presensi::with(['murid', 'jadwal.kelas', 'pencatat'])
            ->whereHas('jadwal', fn($q) => $q->where('id_kelas', $kelasId))
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }
}
