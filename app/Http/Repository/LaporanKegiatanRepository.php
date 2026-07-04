<?php

namespace App\Http\Repository;

use App\Domain\Entity\LaporanKegiatan;
use Illuminate\Pagination\LengthAwarePaginator;

class LaporanKegiatanRepository
{
    public function findAll(int $perPage = 15): LengthAwarePaginator
    {
        return LaporanKegiatan::with(['murid', 'guru.user', 'jadwal.kelas'])
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function findById(int $id): ?LaporanKegiatan
    {
        return LaporanKegiatan::with(['murid', 'guru.user', 'jadwal.kelas', 'balasan.user'])
            ->find($id);
    }

    public function findByMurid(int $muridId, int $perPage = 15): LengthAwarePaginator
    {
        return LaporanKegiatan::with(['guru.user', 'jadwal.kelas', 'balasan'])
            ->where('id_murid', $muridId)
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function findByOrangTua(int $orangTuaId, int $perPage = 15): LengthAwarePaginator
    {
        return LaporanKegiatan::with(['murid', 'guru.user', 'jadwal.kelas', 'balasan'])
            ->whereHas('murid', fn($q) => $q->where('id_orang_tua', $orangTuaId))
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function create(array $data): LaporanKegiatan
    {
        return LaporanKegiatan::create($data);
    }

    public function update(LaporanKegiatan $laporan, array $data): LaporanKegiatan
    {
        $laporan->update($data);
        return $laporan->fresh(['murid', 'guru.user', 'jadwal.kelas']);
    }

    public function delete(LaporanKegiatan $laporan): void
    {
        $laporan->delete();
    }
}
