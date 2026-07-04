<?php

namespace App\Http\Repository;

use App\Domain\Entity\Guru;
use Illuminate\Database\Eloquent\Collection;

class GuruRepository
{
    public function findAll(): Collection
    {
        return Guru::with('user')->get();
    }

    public function findById(int $id): ?Guru
    {
        return Guru::with('user')->find($id);
    }

    public function findByUserId(int $userId): ?Guru
    {
        return Guru::where('id_user', $userId)->first();
    }

    public function create(array $data): Guru
    {
        return Guru::create($data);
    }

    public function update(Guru $guru, array $data): Guru
    {
        $guru->update($data);
        return $guru->fresh('user');
    }

    public function delete(Guru $guru): void
    {
        $guru->delete();
    }
}
