<?php

use Illuminate\Support\Facades\Route;

Route::get('/login', fn() => view('auth.login'))->name('login');

Route::get('/dashboard', fn() => view('dashboard.index'));
Route::get('/artikel', fn() => view('artikel.index'));
Route::get('/presensi', fn() => view('presensi.index'));
Route::get('/presensi/detail', fn() => view('presensi.detail'));
Route::get('/presensi/laporan', fn() => view('presensi.laporan-murid'));
Route::get('/laporan', fn() => view('laporan.index'));
Route::get('/profil', fn() => view('profil.index'));
Route::get('/murid', fn() => view('murid.index'));
Route::get('/users', fn() => view('users.index'));
Route::get('/roles', fn() => view('roles.index'));
Route::get('/notifikasi', fn() => view('notifikasi.index'));
Route::get('/layanan', fn() => view('layanan.index'));

Route::get('/', fn() => redirect('/login'));
