<?php

namespace App\Http\Service;

use App\Common\Exception\BusinessException;
use App\Domain\Entity\Artikel;
use App\Domain\Entity\User;
use App\Domain\Enums\StatusArtikel;
use App\Http\Repository\ArtikelRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ArtikelService
{
    public function __construct(private ArtikelRepository $repo) {}

    public function getAll(User $user, ?string $tipe = null): \Illuminate\Pagination\LengthAwarePaginator
    {
        if ($user->hasRole('admin') || $user->hasRole('guru')) {
            return $this->repo->findAll(10, $tipe);
        }

        return $this->repo->findPublished(10, $tipe);
    }

    public function getById(int $id): Artikel
    {
        $artikel = $this->repo->findById($id);
        if (! $artikel) {
            throw new BusinessException('Artikel tidak ditemukan', 404);
        }

        return $artikel;
    }

    public function create(User $user, array $data, ?UploadedFile $gambar1 = null, ?UploadedFile $gambar2 = null): Artikel
    {
        if ($gambar1) {
            $data['gambar_artikel'] = $gambar1->store('artikel', 'public');
        }
        if ($gambar2) {
            $data['gambar_artikel_2'] = $gambar2->store('artikel', 'public');
        }

        $data['id_user']         = $user->id_user;
        $data['tanggal_publish'] = now();
        $data['status_artikel']  = $data['status_artikel'] ?? StatusArtikel::PUBLISHED->value;
        $data['tipe']            = $data['tipe'] ?? 'tentang_sekolah';

        return $this->repo->create($data);
    }

    public function update(User $user, int $id, array $data, ?UploadedFile $gambar1 = null, ?UploadedFile $gambar2 = null): Artikel
    {
        $artikel = $this->getById($id);

        if (! $user->hasRole('admin') && $artikel->id_user !== $user->id_user) {
            throw new BusinessException('Anda tidak berhak mengedit artikel ini', 403);
        }

        if ($gambar1) {
            if ($artikel->gambar_artikel) {
                Storage::disk('public')->delete($artikel->gambar_artikel);
            }
            $data['gambar_artikel'] = $gambar1->store('artikel', 'public');
        }

        if ($gambar2) {
            if ($artikel->gambar_artikel_2) {
                Storage::disk('public')->delete($artikel->gambar_artikel_2);
            }
            $data['gambar_artikel_2'] = $gambar2->store('artikel', 'public');
        }

        return $this->repo->update($artikel, $data);
    }

    public function delete(User $user, int $id): void
    {
        if (! $user->hasRole('admin')) {
            throw new BusinessException('Hanya admin yang dapat menghapus artikel', 403);
        }

        $artikel = $this->getById($id);

        if ($artikel->gambar_artikel)   Storage::disk('public')->delete($artikel->gambar_artikel);
        if ($artikel->gambar_artikel_2) Storage::disk('public')->delete($artikel->gambar_artikel_2);

        $this->repo->delete($artikel);
    }
}
