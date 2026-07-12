<?php

namespace App\Http\Controllers;

use App\Common\Response\ApiResponse;
use App\Http\Service\AppointmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class AppointmentsController extends Controller
{
    public function __construct(private AppointmentService $service) {}

    // Admin: list all; parent: list only mine
    public function index(): JsonResponse
    {
        $user = JWTAuth::parseToken()->authenticate();
        $appointments = $this->service->listForUser($user);

        return ApiResponse::success($appointments, 'Data appointment berhasil diambil');
    }

    public function me(): JsonResponse
    {
        $user = JWTAuth::parseToken()->authenticate();
        $appointments = $this->service->listMine($user);

        return ApiResponse::success($appointments, 'Data appointment berhasil diambil');
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'from_date' => 'required|date',
            'reason' => 'required|string|max:2000',
            'to_date' => 'nullable|date',
        ]);

        $user = JWTAuth::parseToken()->authenticate();
        $appointment = $this->service->createFromParent($user, $validated);

        return ApiResponse::created($appointment, 'Permintaan konsultasi berhasil dikirim');
    }

    public function approve(int $id): JsonResponse
    {
        $user = JWTAuth::parseToken()->authenticate();
        $appointment = $this->service->approveOrReject($user, $id, 'approved');

        return ApiResponse::success($appointment, 'Permintaan disetujui');
    }

    public function reject(int $id): JsonResponse
    {
        $user = JWTAuth::parseToken()->authenticate();
        $appointment = $this->service->approveOrReject($user, $id, 'rejected');

        return ApiResponse::success($appointment, 'Permintaan ditolak');
    }
}

