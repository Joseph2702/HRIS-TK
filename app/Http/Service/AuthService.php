<?php

namespace App\Http\Service;

use App\Common\Exception\BusinessException;
use App\Domain\Entity\User;
use App\Http\Repository\UserRepository;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService
{
    public function __construct(private UserRepository $userRepo) {}

    public function login(string $email, string $password): array
    {
        $user = $this->userRepo->findByEmail($email);

        if (! $user || ! Hash::check($password, $user->password)) {
            throw new BusinessException('Email atau password salah', 401);
        }

        if ($user->status !== 'aktif') {
            throw new BusinessException('Akun Anda tidak aktif', 403);
        }

        $token = JWTAuth::fromUser($user);

        return [
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60,
            'user' => $this->formatUser($user),
        ];
    }

    public function logout(): void
    {
        JWTAuth::invalidate(JWTAuth::getToken());
    }

    public function refresh(): array
    {
        $token = JWTAuth::refresh(JWTAuth::getToken());
        $user = JWTAuth::setToken($token)->toUser();

        return [
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60,
            'user' => $this->formatUser($user),
        ];
    }

    public function me(User $user): array
    {
        $user->load(['roles', 'guru', 'orangTua']);
        return $this->formatUser($user);
    }

    private function formatUser(User $user): array
    {
        return [
            'id_user' => $user->id_user,
            'nama' => $user->nama,
            'email' => $user->email,
            'no_hp' => $user->no_hp,
            'jenis_kelamin' => $user->jenis_kelamin,
            'foto_profile' => $user->foto_profile,
            'status' => $user->status,
            'roles' => $user->roles->pluck('nama_role'),
        ];
    }
}
