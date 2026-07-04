<?php

namespace App\Http\Service;

use App\Common\Exception\BusinessException;
use App\Domain\Entity\User;
use App\Http\Repository\GuruRepository;
use App\Http\Repository\OrangTuaRepository;
use App\Http\Repository\UserRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfilService
{
    public function __construct(
        private UserRepository $userRepo,
        private GuruRepository $guruRepo,
        private OrangTuaRepository $orangTuaRepo
    ) {}

    public function getProfil(User $user): array
    {
        $user->load(['roles', 'guru', 'orangTua.murid']);

        $profil = [
            'id_user' => $user->id_user,
            'nama' => $user->nama,
            'email' => $user->email,
            'no_hp' => $user->no_hp,
            'jenis_kelamin' => $user->jenis_kelamin,
            'tempat_lahir' => $user->tempat_lahir,
            'tanggal_lahir' => $user->tanggal_lahir,
            'foto_profile' => $user->foto_profile,
            'status' => $user->status,
            'roles' => $user->roles->pluck('nama_role'),
        ];

        if ($user->guru) {
            $profil['guru'] = ['spesialisasi' => $user->guru->spesialisasi];
        }

        if ($user->orangTua) {
            $profil['orang_tua'] = [
                'pekerjaan' => $user->orangTua->pekerjaan,
                'murid' => $user->orangTua->murid,
            ];
        }

        return $profil;
    }

    public function updateProfil(User $user, array $data, ?UploadedFile $foto = null): array
    {
        if ($foto) {
            if ($user->foto_profile) {
                Storage::disk('public')->delete($user->foto_profile);
            }
            $data['foto_profile'] = $foto->store('profil', 'public');
        }

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $allowedFields = ['nama', 'no_hp', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'foto_profile', 'password'];
        $userData = array_intersect_key($data, array_flip($allowedFields));

        $this->userRepo->update($user, $userData);

        if ($user->hasRole('guru') && isset($data['spesialisasi'])) {
            $guru = $this->guruRepo->findByUserId($user->id_user);
            if ($guru) {
                $this->guruRepo->update($guru, ['spesialisasi' => $data['spesialisasi']]);
            }
        }

        if ($user->hasRole('orang_tua') && isset($data['pekerjaan'])) {
            $orangTua = $this->orangTuaRepo->findByUserId($user->id_user);
            if ($orangTua) {
                $this->orangTuaRepo->update($orangTua, ['pekerjaan' => $data['pekerjaan']]);
            }
        }

        $user->refresh();
        return $this->getProfil($user);
    }
}
