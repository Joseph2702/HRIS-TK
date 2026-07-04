<?php

namespace App\Http\Repository;

use App\Domain\Entity\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class UserRepository
{
    public function findAll(int $perPage = 15): LengthAwarePaginator
    {
        return User::with(['roles'])->paginate($perPage);
    }

    public function findById(int $id): ?User
    {
        return User::with(['roles', 'guru', 'orangTua'])->find($id);
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function update(User $user, array $data): User
    {
        $user->update($data);
        return $user->fresh(['roles', 'guru', 'orangTua']);
    }

    public function delete(User $user): void
    {
        $user->delete();
    }

    public function syncRoles(User $user, array $roleIds): void
    {
        $user->roles()->sync($roleIds);
    }

    public function attachRole(User $user, int $roleId): void
    {
        $user->roles()->syncWithoutDetaching([$roleId => ['is_active' => true]]);
    }
}
