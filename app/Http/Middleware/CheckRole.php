<?php

namespace App\Http\Middleware;

use App\Common\Response\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): mixed
    {
        $user = JWTAuth::parseToken()->authenticate();

        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                return $next($request);
            }
        }

        return ApiResponse::unauthorized('Anda tidak memiliki akses untuk fitur ini');
    }
}
