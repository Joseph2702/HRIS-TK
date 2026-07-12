<?php

namespace App\Http\Service;

use App\Common\Exception\BusinessException;
use App\Domain\Entity\Appointment;
use App\Domain\Entity\Murid;
use App\Domain\Entity\OrangTua;
use App\Domain\Entity\User;
use App\Http\Repository\AppointmentRepository;
use App\Http\Repository\MuridRepository;
use App\Http\Repository\OrangTuaRepository;
use Illuminate\Support\Arr;

class AppointmentService
{
    public function __construct(
        private AppointmentRepository $repo,
        private OrangTuaRepository $orangTuaRepo,
        private MuridRepository $muridRepo,
    ) {}

    // Admin: list all; parent: list only by their orang_tua
    public function listForUser(User $user)
    {
        if ($user->hasRole('admin')) {
            return $this->repo->listAll()->values();
        }

        // fallback: parents/guru -> only mine
        return $this->listMine($user);
    }

    public function listMine(User $user)
    {
        $orangTua = $this->orangTuaRepo->findByUserId($user->id_user);
        if (! $orangTua) {
            throw new BusinessException('Profil orang tua tidak ditemukan', 404);
        }

        return $this->repo->listByOrangTua($orangTua->id_orang_tua)->values();
    }

    public function createFromParent(User $user, array $data): Appointment
    {
        if (! $user->hasRole('orang_tua')) {
            throw new BusinessException('Akses tidak diizinkan', 403);
        }

        $orangTua = $this->orangTuaRepo->findByUserId($user->id_user);
        if (! $orangTua) {
            throw new BusinessException('Profil orang tua tidak ditemukan', 404);
        }

        // Saat ini UI hanya kirim reason + from_date. Kita butuh id_murid.
        // Ambil murid pertama yang aktif untuk orang tua (fallback).
        $murid = $this->muridRepo->findFirstActiveByOrangTuaId($orangTua->id_orang_tua);
        if (! $murid) {
            throw new BusinessException('Murid tidak ditemukan', 404);
        }

        // Tidak pakai indikator threshold (sesuai permintaan).
            $payload = [
            'id_orang_tua' => $orangTua->id_orang_tua,
            'id_murid' => $murid->id_murid,
            'id_jadwal' => null,
            'from_date' => $data['from_date'],
            // migration appointments::to_date tidak nullable, jadi pakai dari_date jika tidak dikirim
            'to_date' => Arr::get($data, 'to_date') ?? $data['from_date'],
            'indikator_threshold_rule' => null,
            'reason' => $data['reason'],
            'status' => 'pending',
            'assigned_guru_id' => null,
            'approved_by' => null,
        ];

        return $this->repo->create($payload);
    }

    public function approveOrReject(User $adminUser, int $id, string $nextStatus): Appointment
    {
        if (! $adminUser->hasRole('admin')) {
            throw new BusinessException('Akses tidak diizinkan', 403);
        }

        $appointment = $this->repo->findById($id);
        if (! $appointment) {
            throw new BusinessException('Data tidak ditemukan', 404);
        }

        $appointment->status = $nextStatus;
        $appointment->approved_by = $adminUser->id_user;
        $appointment->save();

        return $appointment->fresh();
    }
}

