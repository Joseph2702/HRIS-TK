<?php

namespace App\Http\Service;

use App\Common\Exception\BusinessException;
use App\Domain\Entity\JadwalKelas;
use App\Domain\Entity\Presensi;
use App\Domain\Entity\User;
use App\Http\Repository\JadwalKelasRepository;
use App\Http\Repository\MuridRepository;
use App\Http\Repository\PresensiRepository;

class PresensiService
{
    public function __construct(
        private PresensiRepository $repo,
        private JadwalKelasRepository $jadwalRepo,
        private MuridRepository $muridRepo
    ) {}

    public function getByJadwal(int $jadwalId): array
    {
        $jadwal = $this->jadwalRepo->findById($jadwalId);
        if (! $jadwal) {
            throw new BusinessException('Jadwal tidak ditemukan', 404);
        }

        $muridList = $this->muridRepo->findByKelas($jadwal->id_kelas);
        $presensiList = $this->repo->findByJadwal($jadwalId)->keyBy('id_murid');

        return $muridList->map(function ($murid) use ($presensiList) {
            $presensi = $presensiList->get($murid->id_murid);
            return [
                'id_murid' => $murid->id_murid,
                'nama_murid' => $murid->nama_murid,
                'presensi' => $presensi ? [
                    'id_presensi' => $presensi->id_presensi,
                    'status_kehadiran' => $presensi->status_kehadiran,
                    'keterangan' => $presensi->keterangan,
                ] : null,
            ];
        })->values()->toArray();
    }

    public function simpanPresensi(User $user, int $jadwalId, array $presensiData): void
    {
        $jadwal = $this->jadwalRepo->findById($jadwalId);
        if (! $jadwal) {
            throw new BusinessException('Jadwal tidak ditemukan', 404);
        }

        foreach ($presensiData as $item) {
            $this->repo->upsert($jadwalId, $item['id_murid'], [
                'status_kehadiran' => $item['status_kehadiran'],
                'keterangan' => $item['keterangan'] ?? null,
                'dicatat_oleh' => $user->id_user,
            ]);
        }
    }

    public function ubahStatus(User $user, int $presensiId, string $status, ?string $keterangan = null): Presensi
    {
        $presensi = $this->repo->findById($presensiId);
        if (! $presensi) {
            throw new BusinessException('Presensi tidak ditemukan', 404);
        }

        $presensi->update([
            'status_kehadiran' => $status,
            'keterangan' => $keterangan,
            'dicatat_oleh' => $user->id_user,
        ]);

        return $presensi->fresh(['murid', 'jadwal']);
    }

    public function historyByKelas(int $kelasId, int $perPage = 15): \Illuminate\Pagination\LengthAwarePaginator
    {
        return $this->repo->historyByKelas($kelasId, $perPage);
    }

    public function getByMurid(int $muridId, int $perPage = 15): \Illuminate\Pagination\LengthAwarePaginator
    {
        return $this->repo->findByMurid($muridId, $perPage);
    }
}
