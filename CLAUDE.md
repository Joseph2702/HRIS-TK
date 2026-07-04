# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Stack
- **Laravel 13** on **PHP 8.3**
- **PostgreSQL** (primary DB). A `database/database.sqlite` exists from Laravel scaffolding but is not the target DB — use Postgres.
- **No payment gateway** — sistem ini tidak menggunakan Midtrans atau payment apapun.
- Frontend tooling: Vite (`npm run dev` / `npm run build`).
- Frontend design: **Figma** — `https://www.figma.com/design/S7Y6yFsJyJjR4aaIZrMDZk/App-Aluna-Monitoring`
- Tests: PHPUnit 12 via `php artisan test`.
- Lint/format: Laravel Pint.

## Commands

```bash
# First-time setup (installs deps, copies .env, generates key, migrates, builds assets)
composer setup

# Full dev loop — runs `artisan serve`, queue worker, pail log tailer, and vite concurrently
composer dev

# Tests (clears config first, then runs artisan test)
composer test

# Single test file / filter
php artisan test --filter=SomeTestName
php artisan test tests/Feature/AuthTest.php

# Lint / format
./vendor/bin/pint                 # fix
./vendor/bin/pint --test          # check only

# Migrations / DB
php artisan migrate
php artisan migrate:fresh --seed
php artisan tinker
```

## Environment
- Postgres runs on **localhost:5432**, user `aluna`, no password (managed via DBeaver locally).
- `.env` already exists; `composer setup` copies from `.env.example` if missing.

---

## 🧠 Architecture Overview

Project ini menggunakan **Layered Architecture** dengan semua layer berada di bawah `app/`. Struktur aktual:

```
app/
├── Http/                          # Presentation + Service + Repository + Infrastructure
│   ├── Controllers/               # Thin controllers (validate → call service → ApiResponse)
│   │   ├── AuthController.php
│   │   ├── ArtikelController.php
│   │   ├── KelasController.php
│   │   ├── PresensiController.php
│   │   ├── LaporanKegiatanController.php
│   │   ├── BalasanLaporanController.php
│   │   ├── MuridController.php
│   │   ├── ProfilController.php
│   │   ├── UserController.php
│   │   └── RoleController.php
│   ├── Service/                   # Business logic utama
│   │   ├── AuthService.php
│   │   ├── ArtikelService.php
│   │   ├── KelasService.php
│   │   ├── PresensiService.php
│   │   ├── LaporanKegiatanService.php
│   │   ├── MuridService.php
│   │   ├── ProfilService.php
│   │   └── UserService.php
│   ├── Repository/                # Akses database (Eloquent)
│   │   ├── UserRepository.php
│   │   ├── ArtikelRepository.php
│   │   ├── KelasRepository.php
│   │   ├── JadwalKelasRepository.php
│   │   ├── PresensiRepository.php
│   │   ├── LaporanKegiatanRepository.php
│   │   ├── BalasanLaporanRepository.php
│   │   ├── MuridRepository.php
│   │   ├── GuruRepository.php
│   │   └── OrangTuaRepository.php
│   └── Config/                    # App config markers (infrastructure)
│       ├── DatabaseConfig.php
│       └── SecurityConfig.php
├── Domain/                        # Model bisnis murni (tidak depend ke Http)
│   ├── Entity/                    # Eloquent models (custom PK: id_xxx)
│   └── Enums/                     # StatusKehadiran, RoleType, StatusArtikel, StatusMurid
├── Common/                        # Shared utilities
│   ├── Response/ApiResponse.php   # JSON envelope (success, error, validationError, dll)
│   └── Exception/BusinessException.php
└── Providers/
```

### Layer responsibilities

| Layer | Namespace | Tanggung jawab |
|-------|-----------|----------------|
| Presentation | `App\Http\Controllers` | HTTP request/response, validasi input, mapping DTO |
| Service | `App\Http\Service` | Business logic, orkestrasi repository |
| Repository | `App\Http\Repository` | Query & persistence logic via Eloquent |
| Domain | `App\Domain\Entity`, `App\Domain\Enums` | Representasi model bisnis, Entity & Enum |
| Infrastructure | `App\Http\Config` | Konfigurasi aplikasi |
| Common | `App\Common` | Exception handling global, standard response wrapper |

## 🔄 Flow Singkat

```
Controller → Service → Repository → Database
```

### Layering conventions
- **Business logic belongs in a Service layer**, not in controllers. Controllers should be thin: validate request → call service → return `ApiResponse`.
- All API responses use `App\Common\Response\ApiResponse` — reuse this wrapper, don't roll per-controller formats.
- **All API endpoints require authentication** except the `/auth/*` group (login, register, refresh, dll).
- Domain entities live in `app/Domain/Entity/` (bukan `app/Models/`). Auth config references `App\Domain\Entity\User`.

---

## 👥 Aktor & Use Case

Sistem bernama **Sistem Informasi Monitoring Aluna**. Ada 3 aktor utama:

### Admin
Akses penuh ke semua fitur setelah login:
- **Autentikasi**: Login → include: Validasi Akses; extend: Tampilkan Pesan Gagal Login | Logout
- **Manajemen Artikel**: Membuat Artikel → include: Tampilkan Pesan Berhasil Upload | Edit Artikel | Hapus Artikel → include: Tampilkan Pesan Berhasil Hapus | View Artikel
- **Manajemen Kelas**: Tambah Kelas | Hapus Kelas → include: Tampilkan Pesan Berhasil Hapus | View History Presensi
- **Manajemen Presensi**: Ubah Status Presensi Murid (Status 'Hadir' / Status 'Tidak Hadir') | Membuat Laporan Kegiatan Murid
- **Manajemen Laporan**: View Laporan Kegiatan Murid | Hapus Laporan → include: Tampilkan Pesan Berhasil Hapus | Kirim Balasan Laporan → include: Tampilkan Pesan Berhasil Terkirim
- **Manajemen Profil**: Edit Profil Pengguna | Menyimpan Profil → include: Tampilkan Pesan Berhasil Simpan
- **Manajemen Pengguna**: Tambah Pengguna | Edit Data & Role Pengguna | Hapus Pengguna → include: Tampilkan Pesan Berhasil Hapus
- **Manajemen Role (RBAC)**: Tambah Role → include: Tampilkan Pesan Berhasil Tambah | Edit Akses Role | Hapus Role

### Guru
Akses terbatas setelah login:
- **Autentikasi**: Login → include: Validasi Akses; extend: Tampilkan Pesan Gagal Login | Logout
- **Manajemen Artikel**: Membuat Artikel → include: Tampilkan Pesan Berhasil Upload | Edit Artikel | View Artikel
- **Manajemen Presensi**: Menyimpan Presensi → include: Tampilkan Pesan Berhasil Simpan | Edit Presensi Murid
- **Manajemen Laporan**: View Laporan Kegiatan Murid | Hapus Laporan → include: Tampilkan Pesan Berhasil Hapus | Kirim Balasan Laporan → include: Tampilkan Pesan Berhasil Terkirim
- **Manajemen Profil**: Edit Profil Pengguna | Menyimpan Profil → include: Tampilkan Pesan Berhasil Simpan

### Orang Tua
Dapat login dan mengelola profil anak:
- **Autentikasi**: Login → include: Validasi Akses; extend: Tampilkan Pesan Gagal Login | Logout
- **Konten**: View Artikel
- **Laporan**: View Laporan Kegiatan Murid | Kirim Balasan Laporan → include: Tampilkan Pesan Berhasil Terkirim
- **Profil**: Edit Profil Pengguna | Tambah Profil Anak | Edit Profil Anak | Menyimpan Profil → include: Tampilkan Pesan Berhasil Simpan

---

## 🔄 Business Flow Utama

### 1. Alur Autentikasi (Login)
```
User input email + password
→ Validasi Akses (cek kredensial di DB)
→ jika gagal: tampilkan Pesan Gagal Login
→ jika berhasil: generate token JWT/session
→ redirect ke dashboard sesuai role (Admin/Guru/Orang Tua)
```

### 2. Alur Manajemen Artikel (Guru/Admin)
```
User (Guru/Admin) buat artikel
→ input judul, konten, gambar
→ simpan ke DB (tabel artikel, status: published)
→ tampilkan Pesan Berhasil Upload

Edit Artikel:
→ load artikel yang ada
→ update field yang diubah
→ tampilkan Pesan Berhasil Upload

Hapus Artikel (Admin only):
→ soft delete / delete artikel
→ tampilkan Pesan Berhasil Hapus

View Artikel:
→ semua aktor dapat melihat artikel yang published
```

### 3. Alur Manajemen Kelas & Jadwal (Admin)
```
Admin tambah kelas
→ input nama_kelas, deskripsi, kapasitas
→ simpan ke tabel kelas

Admin tambah jadwal kelas
→ pilih kelas + guru pengampu
→ input tanggal, jam_mulai, jam_selesai, topik
→ simpan ke tabel jadwal_kelas

Admin hapus kelas
→ cek tidak ada jadwal aktif yang terikat
→ hapus → tampilkan Pesan Berhasil Hapus
```

### 4. Alur Presensi (Guru input / Admin ubah)
```
Guru buka jadwal kelas yang diajar
→ tampil daftar murid per kelas
→ Guru set status kehadiran tiap murid (hadir / tidak hadir / izin / sakit)
→ Menyimpan Presensi → update tabel presensi per id_jadwal + id_murid
→ tampilkan Pesan Berhasil Simpan

Admin ubah status presensi:
→ pilih jadwal → pilih murid → ubah status ke 'hadir' / 'tidak hadir'
→ update tabel presensi

Guru edit presensi:
→ load presensi yang sudah ada
→ ubah status kehadiran → simpan ulang

Admin view history presensi:
→ filter by kelas / jadwal / murid / tanggal
→ tampilkan riwayat presensi
```

### 5. Alur Laporan Kegiatan Murid
```
Admin / Guru buat laporan kegiatan:
→ pilih jadwal_kelas + murid
→ isi judul_laporan, isi_laporan
→ simpan ke tabel laporan_kegiatan

View Laporan (Admin/Guru/Orang Tua):
→ Orang Tua hanya lihat laporan anaknya sendiri
→ Admin/Guru lihat semua laporan

Hapus Laporan (Admin/Guru):
→ hapus dari DB → tampilkan Pesan Berhasil Hapus

Kirim Balasan Laporan (Admin/Guru/Orang Tua):
→ input isi_balasan
→ simpan ke tabel balasan_laporan (id_laporan + id_user)
→ tampilkan Pesan Berhasil Terkirim
```

### 6. Alur Profil & Manajemen Anak (Orang Tua)
```
Orang Tua tambah profil anak:
→ input nama_murid, tanggal_lahir, jenis_kelamin, foto, kelas
→ simpan ke tabel murid (id_orang_tua dari session)
→ Menyimpan Profil → tampilkan Pesan Berhasil Simpan

Edit Profil Anak:
→ load data murid yang dimiliki orang tua
→ update field → simpan

Edit Profil Pengguna (semua aktor):
→ update data diri di tabel users / guru / orang_tua
→ Menyimpan Profil → tampilkan Pesan Berhasil Simpan
```

### 7. Alur Manajemen Pengguna (Admin)
```
Tambah Pengguna:
→ input data user (nama, email, password, no_hp, dll)
→ pilih role (Admin / Guru / Orang Tua)
→ insert ke tabel users + user_roles
→ jika role = Guru: insert ke tabel guru
→ jika role = Orang Tua: insert ke tabel orang_tua

Edit Data & Role Pengguna:
→ update tabel users
→ update user_roles (tambah/hapus role)

Hapus Pengguna:
→ soft delete atau cascade delete
→ tampilkan Pesan Berhasil Hapus
```

### 8. Alur Manajemen Role (Admin)
```
Tambah Role:
→ input nama_role
→ simpan ke tabel roles
→ tampilkan Pesan Berhasil Tambah

Edit Akses Role:
→ load role + permissions yang dimiliki
→ tambah/hapus permission via tabel role_permissions

Hapus Role:
→ cek tidak ada user aktif dengan role ini
→ hapus dari tabel roles (cascade ke role_permissions)
```

---

## 🗄️ Database Schema (Authoritative)

PostgreSQL. This is the source of truth — migrations should match it. Custom PK names (`id_user`, `id_guru`, …) mean Eloquent models need `protected $primaryKey = 'id_xxx';` and often `public $timestamps = false;` pada tabel tanpa `updated_at`.

```sql
-- =========================
-- LEVEL 1: MASTER
-- =========================
CREATE TABLE users (
  id_user        SERIAL PRIMARY KEY,
  nama           VARCHAR(100),
  email          VARCHAR(100) UNIQUE NOT NULL,
  password       VARCHAR(255) NOT NULL,
  no_hp          VARCHAR(20),
  jenis_kelamin  VARCHAR(10),
  tempat_lahir   VARCHAR(100),
  tanggal_lahir  DATE,
  foto_profile   TEXT,
  status         VARCHAR(20) DEFAULT 'aktif',    -- aktif | nonaktif
  created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE roles (
  id_role    SERIAL PRIMARY KEY,
  nama_role  VARCHAR(50) UNIQUE NOT NULL,         -- admin | guru | orang_tua
  is_active  BOOLEAN DEFAULT TRUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE permissions (
  id_permission    SERIAL PRIMARY KEY,
  nama_permission  VARCHAR(100) UNIQUE NOT NULL,
  deskripsi        TEXT
);

CREATE TABLE kelas (
  id_kelas    SERIAL PRIMARY KEY,
  nama_kelas  VARCHAR(100) NOT NULL,
  deskripsi   TEXT,
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =========================
-- LEVEL 2: PROFILE & RBAC
-- =========================
CREATE TABLE guru (
  id_guru       SERIAL PRIMARY KEY,
  id_user       INT REFERENCES users(id_user) ON DELETE CASCADE,
  spesialisasi  VARCHAR(100),
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE orang_tua (
  id_orang_tua  SERIAL PRIMARY KEY,
  id_user       INT REFERENCES users(id_user) ON DELETE CASCADE,
  pekerjaan     VARCHAR(100),
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE user_roles (
  id_user    INT REFERENCES users(id_user) ON DELETE CASCADE,
  id_role    INT REFERENCES roles(id_role) ON DELETE CASCADE,
  is_active  BOOLEAN DEFAULT TRUE,
  PRIMARY KEY (id_user, id_role)
);

CREATE TABLE role_permissions (
  id_role        INT REFERENCES roles(id_role) ON DELETE CASCADE,
  id_permission  INT REFERENCES permissions(id_permission) ON DELETE CASCADE,
  PRIMARY KEY (id_role, id_permission)
);

CREATE TABLE artikel (
  id_artikel      SERIAL PRIMARY KEY,
  id_user         INT REFERENCES users(id_user),  -- pembuat (Guru/Admin)
  judul_artikel   VARCHAR(255) NOT NULL,
  gambar_artikel  TEXT,
  konten_artikel  TEXT,
  status_artikel  VARCHAR(20) DEFAULT 'published', -- published | draft | archived
  tanggal_publish TIMESTAMP,
  created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE activity_log (
  id_log       SERIAL PRIMARY KEY,
  id_user      INT REFERENCES users(id_user),
  modul        VARCHAR(100),
  aktivitas    VARCHAR(100),
  keterangan   TEXT,
  tanggal_log  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =========================
-- LEVEL 3: MURID & JADWAL
-- =========================
CREATE TABLE murid (
  id_murid      SERIAL PRIMARY KEY,
  id_orang_tua  INT REFERENCES orang_tua(id_orang_tua),
  id_kelas      INT REFERENCES kelas(id_kelas),
  nama_murid    VARCHAR(100) NOT NULL,
  tanggal_lahir DATE,
  jenis_kelamin VARCHAR(10),
  foto_murid    TEXT,
  status_murid  VARCHAR(20) DEFAULT 'aktif',      -- aktif | nonaktif
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE jadwal_kelas (
  id_jadwal    SERIAL PRIMARY KEY,
  id_kelas     INT REFERENCES kelas(id_kelas),
  id_guru      INT REFERENCES guru(id_guru),
  tanggal      DATE NOT NULL,
  jam_mulai    TIME,
  jam_selesai  TIME,
  topik        VARCHAR(255),
  created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =========================
-- LEVEL 4: PRESENSI & LAPORAN
-- =========================
CREATE TABLE presensi (
  id_presensi       SERIAL PRIMARY KEY,
  id_jadwal         INT REFERENCES jadwal_kelas(id_jadwal),
  id_murid          INT REFERENCES murid(id_murid),
  status_kehadiran  VARCHAR(20) NOT NULL,          -- hadir | tidak_hadir | izin | sakit
  keterangan        TEXT,
  dicatat_oleh      INT REFERENCES users(id_user), -- id user yang input/ubah
  created_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE (id_jadwal, id_murid)                     -- satu murid satu record per jadwal
);

CREATE TABLE laporan_kegiatan (
  id_laporan      SERIAL PRIMARY KEY,
  id_jadwal       INT REFERENCES jadwal_kelas(id_jadwal),
  id_murid        INT REFERENCES murid(id_murid),
  id_guru         INT REFERENCES guru(id_guru),    -- pembuat laporan
  judul_laporan   VARCHAR(255) NOT NULL,
  isi_laporan     TEXT,
  created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =========================
-- LEVEL 5: BALASAN LAPORAN
-- =========================
CREATE TABLE balasan_laporan (
  id_balasan   SERIAL PRIMARY KEY,
  id_laporan   INT REFERENCES laporan_kegiatan(id_laporan) ON DELETE CASCADE,
  id_user      INT REFERENCES users(id_user),      -- Admin / Guru / Orang Tua
  isi_balasan  TEXT NOT NULL,
  created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =========================
-- INDEXES
-- =========================
CREATE INDEX idx_presensi_jadwal        ON presensi(id_jadwal);
CREATE INDEX idx_presensi_murid         ON presensi(id_murid);
CREATE INDEX idx_laporan_murid          ON laporan_kegiatan(id_murid);
CREATE INDEX idx_laporan_jadwal         ON laporan_kegiatan(id_jadwal);
CREATE INDEX idx_laporan_guru           ON laporan_kegiatan(id_guru);
CREATE INDEX idx_murid_orang_tua        ON murid(id_orang_tua);
CREATE INDEX idx_murid_kelas            ON murid(id_kelas);
CREATE INDEX idx_jadwal_guru            ON jadwal_kelas(id_guru);
CREATE INDEX idx_jadwal_kelas           ON jadwal_kelas(id_kelas);
CREATE INDEX idx_jadwal_tanggal         ON jadwal_kelas(tanggal);
CREATE INDEX idx_artikel_user           ON artikel(id_user);
CREATE INDEX idx_balasan_laporan        ON balasan_laporan(id_laporan);
```

---

## 🧩 Domain Concepts (penting untuk setiap pengerjaan fitur)

- **Users vs Guru vs Orang Tua**: `users` adalah tabel auth/akun. `guru` dan `orang_tua` adalah profile row 1:1 yang di-key oleh `id_user`. Role di-manage many-to-many via `user_roles` (+ `role_permissions` untuk RBAC). Saat membuat akun Guru, insert ke `users` DAN `guru`. Saat membuat akun Orang Tua, insert ke `users` DAN `orang_tua`.

- **Murid sebagai entitas terpisah**: Murid (`murid`) adalah profil anak yang dimiliki oleh `orang_tua`. Satu orang tua bisa punya banyak anak (1:N). Murid terhubung ke `kelas` untuk tahu mereka ada di kelas mana.

- **Presensi per Jadwal per Murid**: `presensi` memiliki UNIQUE constraint `(id_jadwal, id_murid)` — satu murid hanya punya satu record presensi per jadwal. Gunakan `UPSERT` (INSERT ... ON CONFLICT DO UPDATE) saat Guru simpan presensi, bukan insert duplikat.

- **Laporan Kegiatan & Balasan**: Laporan dibuat per murid per jadwal. Balasan bisa dari siapa saja (Admin/Guru/Orang Tua) dan disimpan di `balasan_laporan`. Orang Tua hanya bisa lihat laporan yang `id_murid`-nya adalah anak mereka.

- **RBAC**: Role dan permission dikelola via `roles`, `permissions`, `user_roles`, `role_permissions`. Saat mengecek akses, gunakan middleware yang baca dari `user_roles` + `role_permissions`.

- **Naming**: skema, kolom, dan istilah domain dalam **Bahasa Indonesia** (`murid`, `jadwal_kelas`, `laporan_kegiatan`, dll). Pertahankan konvensi ini di model, migration, dan API payload — jangan diterjemahkan ke Bahasa Inggris.

- **Status Kehadiran** (Enum): `hadir` | `tidak_hadir` | `izin` | `sakit`

- **Status Artikel** (Enum): `published` | `draft` | `archived`

- **Status Murid** (Enum): `aktif` | `nonaktif`

- **Tidak ada payment flow** — sistem ini tidak memproses pembayaran apapun.

---

## 🗺️ Entity Relationship Summary

```
users (1) ──── (1) guru
users (1) ──── (1) orang_tua
users (N) ──── (N) roles           [via user_roles]
roles (N) ──── (N) permissions     [via role_permissions]

orang_tua (1) ──── (N) murid
kelas     (1) ──── (N) murid
kelas     (1) ──── (N) jadwal_kelas
guru      (1) ──── (N) jadwal_kelas

jadwal_kelas (1) ──── (N) presensi
murid        (1) ──── (N) presensi

jadwal_kelas     (1) ──── (N) laporan_kegiatan
murid            (1) ──── (N) laporan_kegiatan
guru             (1) ──── (N) laporan_kegiatan

laporan_kegiatan (1) ──── (N) balasan_laporan
users            (1) ──── (N) balasan_laporan

users (1) ──── (N) artikel
users (1) ──── (N) activity_log
```

---

## 🔐 Akses Kontrol Per Fitur

| Fitur | Admin | Guru | Orang Tua |
|-------|-------|------|-----------|
| Login / Logout | ✅ | ✅ | ✅ |
| Buat Artikel | ✅ | ✅ | ❌ |
| Edit Artikel | ✅ | ✅ (milik sendiri) | ❌ |
| Hapus Artikel | ✅ | ❌ | ❌ |
| View Artikel | ✅ | ✅ | ✅ |
| Tambah/Hapus Kelas | ✅ | ❌ | ❌ |
| View History Presensi | ✅ | ❌ | ❌ |
| Input/Edit Presensi | ✅ | ✅ | ❌ |
| Buat Laporan Kegiatan | ✅ | ✅ | ❌ |
| View Laporan | ✅ | ✅ | ✅ (anak sendiri) |
| Hapus Laporan | ✅ | ✅ | ❌ |
| Kirim Balasan Laporan | ✅ | ✅ | ✅ |
| Tambah/Edit Profil Anak | ❌ | ❌ | ✅ |
| Edit Profil Sendiri | ✅ | ✅ | ✅ |
| Manajemen Pengguna | ✅ | ❌ | ❌ |
| Manajemen Role | ✅ | ❌ | ❌ |