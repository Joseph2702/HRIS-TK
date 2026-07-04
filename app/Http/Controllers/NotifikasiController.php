<?php

namespace App\Http\Controllers;

use App\Common\Response\ApiResponse;
use App\Http\Repository\NotifikasiRepository;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Facades\JWTAuth;

class NotifikasiController extends Controller
{
    public function __construct(private NotifikasiRepository $repo) {}

    public function index(): JsonResponse
    {
        $user = JWTAuth::parseToken()->authenticate();
        $data = $this->repo->findByUser($user->id_user);
        $unread = $this->repo->countUnread($user->id_user);

        return ApiResponse::success(['notifikasi' => $data, 'unread' => $unread]);
    }

    public function markRead(): JsonResponse
    {
        $user = JWTAuth::parseToken()->authenticate();
        $this->repo->markAllRead($user->id_user);

        return ApiResponse::success(null, 'Semua notifikasi telah dibaca');
    }
}
