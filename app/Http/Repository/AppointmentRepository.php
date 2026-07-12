<?php

namespace App\Http\Repository;

use App\Domain\Entity\Appointment;
use App\Domain\Entity\Murid;
use App\Domain\Entity\OrangTua;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class AppointmentRepository
{
    public function create(array $data): Appointment
    {
        return Appointment::create($data);
    }

    public function findById(int $id): ?Appointment
    {
        return Appointment::query()
            ->where('id_appointment', $id)
            ->first();
    }

    public function listAll(): Collection
    {
        return Appointment::query()
            ->orderByDesc('created_at')
            ->get();
    }

    public function listByOrangTua(int $orangTuaId): Collection
    {
        return Appointment::query()
            ->where('id_orang_tua', $orangTuaId)
            ->orderByDesc('created_at')
            ->get();
    }
}

