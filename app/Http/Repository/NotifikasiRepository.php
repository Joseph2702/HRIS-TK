<?php

namespace App\Http\Repository;

use App\Domain\Entity\Notifikasi;
use Illuminate\Database\Eloquent\Collection;

class NotifikasiRepository
{
    public function findByUser(int $userId, int $limit = 30): Collection
    {
        return Notifikasi::where('id_user', $userId)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    public function countUnread(int $userId): int
    {
        return Notifikasi::where('id_user', $userId)->where('is_read', false)->count();
    }

    public function markAllRead(int $userId): void
    {
        Notifikasi::where('id_user', $userId)->update(['is_read' => true]);
    }

    public function create(array $data): Notifikasi
    {
        return Notifikasi::create($data);
    }

    public function kirim(int $idUser, string $judul, string $pesan, string $tipe, ?int $idReferensi = null): void
    {
        $this->create([
            'id_user'       => $idUser,
            'judul'         => $judul,
            'pesan'         => $pesan,
            'tipe'          => $tipe,
            'id_referensi'  => $idReferensi,
            'is_read'       => false,
        ]);
    }
}
