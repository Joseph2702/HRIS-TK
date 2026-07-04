<?php

namespace App\Http\Service;

use App\Common\Exception\BusinessException;
use App\Domain\Entity\Murid;
use App\Domain\Entity\User;
use App\Http\Repository\MuridRepository;
use App\Http\Repository\OrangTuaRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MuridService
{
    public function __construct(
        private MuridRepository $repo,
        private OrangTuaRepository $orangTuaRepo
    ) {}

    public function getAll(User $user): mixed
    {
        if ($user->hasRole('orang_tua')) {
            $orangTua = $this->orangTuaRepo->findByUserId($user->id_user);
            if (! $orangTua) {
                throw new BusinessException('Profil orang tua tidak ditemukan', 404);
            }
            return $this->repo->findByOrangTua($orangTua->id_orang_tua);
        }

        return $this->repo->findAll();
    }

    public function getById(User $user, int $id): Murid
    {
        $murid = $this->repo->findById($id);
        if (! $murid) {
            throw new BusinessException('Murid tidak ditemukan', 404);
        }

        if ($user->hasRole('orang_tua')) {
            $orangTua = $this->orangTuaRepo->findByUserId($user->id_user);
            if (! $orangTua || $murid->id_orang_tua !== $orangTua->id_orang_tua) {
                throw new BusinessException('Akses tidak diizinkan', 403);
            }
        }

        return $murid;
    }

    public function create(User $user, array $data, ?UploadedFile $foto = null): Murid
    {
        if (! $user->hasRole('orang_tua')) {
            throw new BusinessException('Hanya orang tua yang dapat menambah profil anak', 403);
        }

        $orangTua = $this->orangTuaRepo->findByUserId($user->id_user);
        if (! $orangTua) {
            throw new BusinessException('Profil orang tua tidak ditemukan', 404);
        }

        if ($foto) {
            $data['foto_murid'] = $foto->store('murid', 'public');
        }

        $data['id_orang_tua'] = $orangTua->id_orang_tua;
        return $this->repo->create($data);
    }

    public function update(User $user, int $id, array $data, ?UploadedFile $foto = null): Murid
    {
        $murid = $this->getById($user, $id);

        if ($foto) {
            if ($murid->foto_murid) {
                Storage::disk('public')->delete($murid->foto_murid);
            }
            $data['foto_murid'] = $foto->store('murid', 'public');
        }

        return $this->repo->update($murid, $data);
    }

    public function delete(User $user, int $id): void
    {
        $murid = $this->getById($user, $id);

        if ($murid->foto_murid) {
            Storage::disk('public')->delete($murid->foto_murid);
        }

        $this->repo->delete($murid);
    }
}
