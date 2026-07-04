<?php

namespace App\Http\Service;

use App\Common\Exception\BusinessException;
use App\Domain\Entity\Role;
use App\Domain\Entity\User;
use App\Domain\Enums\RoleType;
use App\Http\Repository\GuruRepository;
use App\Http\Repository\OrangTuaRepository;
use App\Http\Repository\UserRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function __construct(
        private UserRepository $userRepo,
        private GuruRepository $guruRepo,
        private OrangTuaRepository $orangTuaRepo
    ) {}

    public function getAll(): LengthAwarePaginator
    {
        return $this->userRepo->findAll();
    }

    public function getById(int $id): User
    {
        $user = $this->userRepo->findById($id);
        if (! $user) {
            throw new BusinessException('Pengguna tidak ditemukan', 404);
        }

        return $user;
    }

    public function create(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $roleNames = $data['roles'] ?? [];
            unset($data['roles'], $data['spesialisasi'], $data['pekerjaan']);

            $data['password'] = Hash::make($data['password']);
            $user = $this->userRepo->create($data);

            $roleIds = Role::whereIn('nama_role', $roleNames)->pluck('id_role');
            $this->userRepo->syncRoles($user, $roleIds->toArray());

            foreach ($roleNames as $roleName) {
                if ($roleName === RoleType::GURU->value) {
                    $this->guruRepo->create(['id_user' => $user->id_user, 'spesialisasi' => $data['spesialisasi'] ?? null]);
                }
                if ($roleName === RoleType::ORANG_TUA->value) {
                    $this->orangTuaRepo->create(['id_user' => $user->id_user, 'pekerjaan' => $data['pekerjaan'] ?? null]);
                }
            }

            return $user->fresh(['roles', 'guru', 'orangTua']);
        });
    }

    public function update(int $id, array $data): User
    {
        return DB::transaction(function () use ($id, $data) {
            $user = $this->getById($id);
            $roleNames = $data['roles'] ?? null;
            unset($data['roles']);

            if (! empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }

            $this->userRepo->update($user, $data);

            if ($roleNames !== null) {
                $roleIds = Role::whereIn('nama_role', $roleNames)->pluck('id_role');
                $this->userRepo->syncRoles($user, $roleIds->toArray());
            }

            return $user->fresh(['roles', 'guru', 'orangTua']);
        });
    }

    public function delete(int $id): void
    {
        $user = $this->getById($id);
        $this->userRepo->delete($user);
    }

    public function getRoles(): \Illuminate\Database\Eloquent\Collection
    {
        return Role::all();
    }

    public function createRole(array $data): Role
    {
        return Role::create($data);
    }

    public function updateRole(int $roleId, array $data): Role
    {
        $role = Role::find($roleId);
        if (! $role) {
            throw new BusinessException('Role tidak ditemukan', 404);
        }
        $role->update($data);
        return $role->fresh();
    }

    public function deleteRole(int $roleId): void
    {
        $role = Role::find($roleId);
        if (! $role) {
            throw new BusinessException('Role tidak ditemukan', 404);
        }

        if ($role->users()->wherePivot('is_active', true)->exists()) {
            throw new BusinessException('Role tidak dapat dihapus karena masih memiliki pengguna aktif', 409);
        }

        $role->delete();
    }

    public function syncRolePermissions(int $roleId, array $permissionIds): Role
    {
        $role = Role::find($roleId);
        if (! $role) {
            throw new BusinessException('Role tidak ditemukan', 404);
        }

        $role->permissions()->sync($permissionIds);
        return $role->load('permissions');
    }
}
