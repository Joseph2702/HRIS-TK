<?php

namespace App\Http\Service;

use App\Common\Exception\BusinessException;
use App\Domain\Entity\Kelas;
use App\Http\Repository\JadwalKelasRepository;
use App\Http\Repository\KelasRepository;

class KelasService
{
    public function __construct(
        private KelasRepository $repo,
        private JadwalKelasRepository $jadwalRepo
    ) {}

    public function getAll(): \Illuminate\Pagination\LengthAwarePaginator
    {
        return $this->repo->findAll();
    }

    public function getById(int $id): Kelas
    {
        $kelas = $this->repo->findById($id);
        if (! $kelas) {
            throw new BusinessException('Kelas tidak ditemukan', 404);
        }

        return $kelas;
    }

    public function create(array $data): Kelas
    {
        return $this->repo->create($data);
    }

    public function update(int $id, array $data): Kelas
    {
        $kelas = $this->getById($id);
        return $this->repo->update($kelas, $data);
    }

    public function delete(int $id): void
    {
        $kelas = $this->getById($id);

        if ($this->repo->hasActiveJadwal($id)) {
            throw new BusinessException('Kelas tidak dapat dihapus karena masih memiliki jadwal aktif', 409);
        }

        $this->repo->delete($kelas);
    }

    public function getJadwal(int $kelasId): \Illuminate\Database\Eloquent\Collection
    {
        $this->getById($kelasId);
        return $this->jadwalRepo->findByKelas($kelasId);
    }

    public function tambahJadwal(int $kelasId, array $data): \App\Domain\Entity\JadwalKelas
    {
        $this->getById($kelasId);
        $data['id_kelas'] = $kelasId;
        return $this->jadwalRepo->create($data);
    }
}
