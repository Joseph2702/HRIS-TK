<?php

namespace App\Http\Repository;

use App\Domain\Entity\OrangTua;

class OrangTuaRepository
{
    public function findById(int $id): ?OrangTua
    {
        return OrangTua::with(['user', 'murid'])->find($id);
    }

    public function findByUserId(int $userId): ?OrangTua
    {
        return OrangTua::where('id_user', $userId)->first();
    }

    public function create(array $data): OrangTua
    {
        return OrangTua::create($data);
    }

    public function update(OrangTua $orangTua, array $data): OrangTua
    {
        $orangTua->update($data);
        return $orangTua->fresh(['user', 'murid']);
    }
}
