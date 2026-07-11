# TODO - Fitur Feedback/Ulasan Layanan

## Backend
- [x] Buat migration tabel `ulasan_layanan`
  - FK: `id_artikel` -> `artikel.id_artikel`
  - FK: `id_user` -> `users.id_user`
  - Kolom: `rating` (1-5), `isi_ulasan` (text), `created_at`
  - Unique: `(id_artikel, id_user)`
- [x] Buat entity `app/Domain/Entity/UlasanLayanan.php`
- [x] Tambahkan API endpoint di `routes/api.php`
  - `GET /ulasan-layanan` untuk ambil rating overall + daftar ulasan
  - `POST /ulasan-layanan` untuk orang_tua submit ulasan
- [x] Buat controller (dan repository) untuk query:
  - hitung `overall_avg_rating` dan `overall_total_reviews`
  - ambil daftar ulasan (dengan info user/isi/rating)
  - upsert logic jika unique `(id_artikel,id_user)` sudah ada

## Frontend
- [x] Buat view baru `resources/views/ulasan-layanan/index.blade.php`
  - H1: rating keseluruhan layanan sekolah
  - Button kanan-bawah: “Tambah Ulasan” (hanya untuk orang_tua)
  - List ulasan + rating
- [x] Tambahkan link menu di sidebar paling bawah pada `resources/views/layouts/app.blade.php`
  - Menu tampil untuk admin + orang_tua, dengan form hanya untuk orang_tua

## Testing
- [ ] Jalankan `php artisan migrate`
- [ ] Cek UI:
  - orang_tua bisa submit ulasan
  - admin bisa lihat rating overall + daftar ulasan
- [ ] Validasi rating (1-5) dan mencegah duplikasi ulasan per (id_artikel, id_user)
