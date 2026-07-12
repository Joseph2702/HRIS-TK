<?php

use App\Http\Controllers\ArtikelController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BalasanLaporanController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\LaporanKegiatanController;
use App\Http\Controllers\MuridController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UlasanLayananController;
use Illuminate\Support\Facades\Route;

// ─── Auth (Public) ──────────────────────────────────────────
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('refresh', [AuthController::class, 'refresh'])->middleware('jwt.auth');
    Route::post('logout', [AuthController::class, 'logout'])->middleware('jwt.auth');
    Route::get('me', [AuthController::class, 'me'])->middleware('jwt.auth');
});

// ─── Protected Routes ────────────────────────────────────────
Route::middleware('jwt.auth')->group(function () {

    // Profil (semua aktor)
    Route::get('profil', [ProfilController::class, 'show']);
    Route::post('profil', [ProfilController::class, 'update']);

    // Artikel (semua bisa view; Admin/Guru bisa CRUD)
    Route::get('artikel', [ArtikelController::class, 'index']);
    Route::get('artikel/{id}', [ArtikelController::class, 'show']);
    Route::post('artikel', [ArtikelController::class, 'store'])->middleware('role:admin,guru');
    Route::post('artikel/{id}', [ArtikelController::class, 'update'])->middleware('role:admin,guru');
    Route::delete('artikel/{id}', [ArtikelController::class, 'destroy'])->middleware('role:admin');

    // Murid
    Route::get('murid', [MuridController::class, 'index']);
    Route::get('murid/{id}', [MuridController::class, 'show']);
    Route::post('murid', [MuridController::class, 'store'])->middleware('role:orang_tua');
    Route::post('murid/{id}', [MuridController::class, 'update'])->middleware('role:orang_tua');
    Route::post('murid/{id}/kelas', [MuridController::class, 'assignKelas'])->middleware('role:admin');
    Route::delete('murid/{id}', [MuridController::class, 'destroy'])->middleware('role:orang_tua');

    // Kelas (Admin only untuk CRUD, semua bisa lihat)
    Route::get('kelas', [KelasController::class, 'index']);
    Route::get('kelas/{id}', [KelasController::class, 'show']);
    Route::post('kelas', [KelasController::class, 'store'])->middleware('role:admin');
    Route::put('kelas/{id}', [KelasController::class, 'update'])->middleware('role:admin');
    Route::delete('kelas/{id}', [KelasController::class, 'destroy'])->middleware('role:admin');
    Route::get('kelas/{id}/jadwal', [KelasController::class, 'jadwal']);
    Route::post('kelas/{id}/jadwal', [KelasController::class, 'tambahJadwal'])->middleware('role:admin');

    // Presensi
    Route::get('jadwal/{jadwalId}/presensi', [PresensiController::class, 'byJadwal']);
    Route::post('jadwal/{jadwalId}/presensi', [PresensiController::class, 'simpan'])->middleware('role:admin,guru');
    Route::put('presensi/{id}/status', [PresensiController::class, 'ubahStatus'])->middleware('role:admin,guru');
    Route::get('kelas/{id}/presensi/history', [PresensiController::class, 'history'])->middleware('role:admin');
    Route::get('murid/{id}/presensi', [PresensiController::class, 'byMurid']);

    // Laporan Kegiatan
    Route::get('laporan', [LaporanKegiatanController::class, 'index']);
    Route::get('laporan/{id}', [LaporanKegiatanController::class, 'show']);
    Route::post('laporan', [LaporanKegiatanController::class, 'store'])->middleware('role:admin,guru');
    Route::put('laporan/{id}', [LaporanKegiatanController::class, 'update'])->middleware('role:admin,guru');
    Route::delete('laporan/{id}', [LaporanKegiatanController::class, 'destroy'])->middleware('role:admin,guru');
    Route::post('laporan/{id}/balas', [LaporanKegiatanController::class, 'balas']);
    Route::get('laporan/trend/data', [LaporanKegiatanController::class, 'trend']);
    Route::delete('balasan/{id}', [BalasanLaporanController::class, 'destroy'])->middleware('role:admin,guru');

    // Konsultasi / Appointments
    Route::get('appointments', [\App\Http\Controllers\AppointmentsController::class, 'index']);
    Route::get('appointments/me', [\App\Http\Controllers\AppointmentsController::class, 'me']);
    Route::post('appointments', [\App\Http\Controllers\AppointmentsController::class, 'store'])->middleware('role:orang_tua');
    Route::put('appointments/{id}/approve', [\App\Http\Controllers\AppointmentsController::class, 'approve'])->middleware('role:admin');
    Route::put('appointments/{id}/reject', [\App\Http\Controllers\AppointmentsController::class, 'reject'])->middleware('role:admin');

    // Ulasan Layanan
    Route::get('ulasan-layanan', [UlasanLayananController::class, 'index']);
    Route::post('ulasan-layanan', [UlasanLayananController::class, 'store'])->middleware('role:orang_tua');


    // Notifikasi
    Route::get('notifikasi', [\App\Http\Controllers\NotifikasiController::class, 'index']);
    Route::post('notifikasi/read', [\App\Http\Controllers\NotifikasiController::class, 'markRead']);

    // Manajemen Pengguna (Admin only)
    Route::middleware('role:admin')->group(function () {
        Route::get('users', [UserController::class, 'index']);
        Route::post('users', [UserController::class, 'store']);
        Route::get('users/{id}', [UserController::class, 'show']);
        Route::put('users/{id}', [UserController::class, 'update']);
        Route::delete('users/{id}', [UserController::class, 'destroy']);

        // Manajemen Role (Admin only)
        Route::get('roles', [RoleController::class, 'index']);
        Route::post('roles', [RoleController::class, 'store']);
        Route::put('roles/{id}', [RoleController::class, 'update']);
        Route::delete('roles/{id}', [RoleController::class, 'destroy']);
        Route::post('roles/{id}/permissions', [RoleController::class, 'syncPermissions']);
    });
});
