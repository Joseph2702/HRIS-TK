<?php

namespace App\Http\Middleware;

use App\Common\Response\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtAuthenticate
{
    public function handle(Request $request, Closure $next): mixed
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            if (! $user) {
                return ApiResponse::unauthenticated();
            }
        } catch (TokenExpiredException) {
            return ApiResponse::unauthenticated('Token telah kadaluarsa');
        } catch (JWTException) {
            return ApiResponse::unauthenticated('Token tidak valid');
        }

        return $next($request);
    }
}
