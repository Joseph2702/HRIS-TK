<?php

namespace App\Http\Middleware;

use App\Common\Response\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string ...$permissions): mixed
    {
        $user = JWTAuth::parseToken()->authenticate();

        foreach ($permissions as $permission) {
            // admin bypass
            if (method_exists($user, 'hasRole') && $user->hasRole('admin')) {
                return $next($request);
            }

            // user.permissions comes from AuthService::formatUser() on /auth/me,
            // but middleware operates on JWT subject. So we check directly via roles.permissions.
            // If role-permissions relationship is available, we can check it here.
            if (method_exists($user, 'roles')) {
                $has = $user->roles
                    ->where('is_active', true)
                    ->flatMap(fn($role) => $role->permissions->pluck('nama_permission') ?? collect())
                    ->contains($permission);

                if ($has) {
                    return $next($request);
                }
            }
        }

        return ApiResponse::unauthorized('Anda tidak memiliki akses untuk fitur ini');
    }
}

