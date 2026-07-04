<?php

namespace App\Http\Controllers;

use App\Common\Response\ApiResponse;
use App\Http\Service\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function __construct(private UserService $service) {}

    public function index(): JsonResponse
    {
        return ApiResponse::success($this->service->getRoles());
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'nama_role' => 'required|string|max:50|unique:roles,nama_role',
            'is_active' => 'boolean',
        ]);

        $role = $this->service->createRole($request->only(['nama_role', 'is_active']));

        return ApiResponse::created($role, 'Role berhasil ditambahkan');
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'nama_role' => 'sometimes|string|max:50|unique:roles,nama_role,'.$id.',id_role',
            'is_active' => 'boolean',
        ]);

        $role = $this->service->updateRole($id, $request->only(['nama_role', 'is_active']));

        return ApiResponse::success($role, 'Role berhasil diperbarui');
    }

    public function destroy(int $id): JsonResponse
    {
        $this->service->deleteRole($id);

        return ApiResponse::success(null, 'Role berhasil dihapus');
    }

    public function syncPermissions(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'permission_ids' => 'required|array',
            'permission_ids.*' => 'integer|exists:permissions,id_permission',
        ]);

        $role = $this->service->syncRolePermissions($id, $request->permission_ids);

        return ApiResponse::success($role, 'Akses role berhasil diperbarui');
    }
}
