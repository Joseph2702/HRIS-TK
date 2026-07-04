<?php

namespace App\Http\Repository;

use App\Domain\Entity\JadwalKelas;
use Illuminate\Pagination\LengthAwarePaginator;

class JadwalKelasRepository
{
    public function findAll(int $perPage = 15): LengthAwarePaginator
    {
        return JadwalKelas::with(['kelas', 'guru.user'])
            ->orderByDesc('tanggal')
            ->paginate($perPage);
    }

    public function findById(int $id): ?JadwalKelas
    {
        return JadwalKelas::with(['kelas', 'guru.user', 'presensi.murid'])->find($id);
    }

    public function findByGuru(int $guruId, int $perPage = 15): LengthAwarePaginator
    {
        return JadwalKelas::with(['kelas', 'guru.user'])
            ->where('id_guru', $guruId)
            ->orderByDesc('tanggal')
            ->paginate($perPage);
    }

    public function findByKelas(int $kelasId): \Illuminate\Database\Eloquent\Collection
    {
        return JadwalKelas::with(['guru.user'])
            ->where('id_kelas', $kelasId)
            ->orderByDesc('tanggal')
            ->get();
    }

    public function create(array $data): JadwalKelas
    {
        return JadwalKelas::create($data);
    }

    public function update(JadwalKelas $jadwal, array $data): JadwalKelas
    {
        $jadwal->update($data);
        return $jadwal->fresh(['kelas', 'guru.user']);
    }

    public function delete(JadwalKelas $jadwal): void
    {
        $jadwal->delete();
    }
}
