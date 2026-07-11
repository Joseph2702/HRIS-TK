<?php

namespace App\Http\Service;

use App\Common\Exception\BusinessException;
use App\Domain\Entity\LaporanKegiatan;
use App\Domain\Entity\User;
use App\Http\Repository\BalasanLaporanRepository;
use App\Http\Repository\GuruRepository;
use App\Http\Repository\LaporanKegiatanRepository;
use App\Http\Repository\MuridRepository;
use App\Http\Repository\NotifikasiRepository;
use App\Http\Repository\OrangTuaRepository;

class LaporanKegiatanService
{
    public function __construct(
        private LaporanKegiatanRepository $repo,
        private BalasanLaporanRepository  $balasanRepo,
        private GuruRepository            $guruRepo,
        private OrangTuaRepository        $orangTuaRepo,
        private MuridRepository           $muridRepo,
        private NotifikasiRepository      $notifRepo,
    ) {}

    public function getAll(User $user, ?int $muridId = null): \Illuminate\Pagination\LengthAwarePaginator
    {
        if ($user->hasRole('orang_tua')) {
            $orangTua = $this->orangTuaRepo->findByUserId($user->id_user);
            if (! $orangTua) {
                throw new BusinessException('Profil orang tua tidak ditemukan', 404);
            }

            return $this->repo->findByOrangTua($orangTua->id_orang_tua, 15, $muridId);
        }

        if ($muridId !== null) {
            return $this->repo->findByMurid($muridId);
        }

        return $this->repo->findAll();
    }

    public function getById(User $user, int $id): LaporanKegiatan
    {
        $laporan = $this->repo->findById($id);
        if (! $laporan) {
            throw new BusinessException('Laporan tidak ditemukan', 404);
        }

        if ($user->hasRole('orang_tua')) {
            $orangTua = $this->orangTuaRepo->findByUserId($user->id_user);
            if (! $orangTua || $laporan->murid->id_orang_tua !== $orangTua->id_orang_tua) {
                throw new BusinessException('Akses tidak diizinkan', 403);
            }
        }

        return $laporan;
    }

    public function create(User $user, array $data): LaporanKegiatan
    {
        $guru = $this->guruRepo->findByUserId($user->id_user);
        if (! $guru && ! $user->hasRole('admin')) {
            throw new BusinessException('Hanya Guru atau Admin yang dapat membuat laporan', 403);
        }

        $data['id_guru'] = $guru?->id_guru;
        $laporan = $this->repo->create($data);

        // Kirim notifikasi ke orang tua murid
        $murid = $this->muridRepo->findById($data['id_murid']);
        if ($murid?->orangTua?->id_user) {
            $namaGuru = $user->nama ?? 'Guru';
            $this->notifRepo->kirim(
                $murid->orangTua->id_user,
                'Laporan Kegiatan Baru',
                "{$namaGuru} telah membuat laporan kegiatan untuk {$murid->nama_murid}: {$laporan->judul_laporan}",
                'laporan',
                $laporan->id_laporan
            );
        }

        return $laporan;
    }

    public function update(User $user, int $id, array $data): LaporanKegiatan
    {
        $laporan = $this->repo->findById($id);
        if (! $laporan) {
            throw new BusinessException('Laporan tidak ditemukan', 404);
        }

        return $this->repo->update($laporan, $data);
    }

    public function delete(User $user, int $id): void
    {
        $laporan = $this->repo->findById($id);
        if (! $laporan) {
            throw new BusinessException('Laporan tidak ditemukan', 404);
        }

        $this->repo->delete($laporan);
    }

    public function kirimBalasan(User $user, int $laporanId, string $isiBalasan): \App\Domain\Entity\BalasanLaporan
    {
        $laporan = $this->repo->findById($laporanId);
        if (! $laporan) {
            throw new BusinessException('Laporan tidak ditemukan', 404);
        }

        if ($user->hasRole('orang_tua')) {
            $orangTua = $this->orangTuaRepo->findByUserId($user->id_user);
            if (! $orangTua || $laporan->murid->id_orang_tua !== $orangTua->id_orang_tua) {
                throw new BusinessException('Akses tidak diizinkan', 403);
            }
        }

        // Guru tidak boleh membalas komentar yang sudah dibalas oleh orang tua
        if ($user->hasRole('guru')) {
            $laporan->load('balasan.user');
            $sudahAdaBalasanOrangTua = $laporan->balasan->contains(function ($b) {
                return $b->user?->hasRole('orang_tua');
            });
            if ($sudahAdaBalasanOrangTua) {
                throw new BusinessException('Guru tidak dapat membalas komentar orang tua. Buat laporan baru jika ingin memberikan catatan tambahan.', 403);
            }
        }

        $balasan = $this->balasanRepo->create([
            'id_laporan' => $laporanId,
            'id_user'    => $user->id_user,
            'isi_balasan' => $isiBalasan,
        ]);

        // Kirim notifikasi ke guru pembuat laporan (jika orang tua yang balas)
        if ($user->hasRole('orang_tua') && $laporan->guru?->user) {
            $this->notifRepo->kirim(
                $laporan->guru->user->id_user,
                'Balasan Laporan Kegiatan',
                "{$user->nama} membalas laporan {$laporan->judul_laporan}: \"{$isiBalasan}\"",
                'balasan',
                $laporanId
            );
        }

        // Kirim notifikasi ke orang tua jika guru/admin yang balas
        if (! $user->hasRole('orang_tua')) {
            $murid = $laporan->murid;
            if ($murid?->orangTua?->id_user) {
                $this->notifRepo->kirim(
                    $murid->orangTua->id_user,
                    'Balasan Laporan Kegiatan',
                    "{$user->nama} membalas laporan {$laporan->judul_laporan}: \"{$isiBalasan}\"",
                    'balasan',
                    $laporanId
                );
            }
        }

        return $balasan;
    }

    public function getTrendData(User $user, ?int $klasId = null, ?int $muridId = null, ?string $fromDate = null, ?string $toDate = null): array
    {
        // For parent: can only see their own child's data
        if ($user->hasRole('orang_tua')) {
            $orangTua = $this->orangTuaRepo->findByUserId($user->id_user);
            if (! $orangTua) {
                throw new BusinessException('Profil orang tua tidak ditemukan', 404);
            }

            // Parents can have multiple children: aggregate across ALL their children
            $muridList = $this->muridRepo->findByOrangTua($orangTua->id_orang_tua);
            if ($muridList->isEmpty()) {
                return [];
            }

            $muridIds = $muridList->pluck('id_murid')->filter()->values()->all();
            return $this->repo->aggregateByDateForMuridIds($klasId, $muridIds, $fromDate, $toDate);
        }

        return $this->repo->aggregateByDate($klasId, $muridId, $fromDate, $toDate);

    }
}

