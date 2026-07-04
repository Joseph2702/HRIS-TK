<?php

namespace App\Http\Controllers;

use App\Common\Response\ApiResponse;
use App\Http\Service\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(private UserService $service) {}

    public function index(): JsonResponse
    {
        return ApiResponse::success($this->service->getAll());
    }

    public function show(int $id): JsonResponse
    {
        return ApiResponse::success($this->service->getById($id));
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'no_hp' => 'nullable|string|max:20',
            'jenis_kelamin' => 'nullable|in:laki-laki,perempuan',
            'roles' => 'required|array|min:1',
            'roles.*' => 'string|in:admin,guru,orang_tua',
            'spesialisasi' => 'nullable|string|max:100',
            'pekerjaan' => 'nullable|string|max:100',
        ]);

        $user = $this->service->create($request->all());

        return ApiResponse::created($user, 'Pengguna berhasil ditambahkan');
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'nama'     => 'sometimes|string|max:100',
            'email'    => 'sometimes|email|unique:users,email,'.$id.',id_user',
            'password' => 'nullable|string|min:8|confirmed',
            'no_hp'    => 'nullable|string|max:20',
            'status'   => 'nullable|in:aktif,nonaktif',
            'roles'    => 'sometimes|array',
            'roles.*'  => 'string|in:admin,guru,orang_tua',
        ]);

        $user = $this->service->update($id, $request->all());

        return ApiResponse::success($user, 'Pengguna berhasil diperbarui');
    }

    public function destroy(int $id): JsonResponse
    {
        $this->service->delete($id);

        return ApiResponse::success(null, 'Pengguna berhasil dihapus');
    }
}
