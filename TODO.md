# Implementation Progress

## ✅ Task 1: ID Murid with Year Prefix (e.g., 2026-012)

### Database & Migration
- [x] 1. Create migration to alter `id_murid` to varchar(20), update FKs in presensi & laporan_kegiatan
- [x] 2. Migration includes script to update existing data

### Backend
- [x] 3. Update `app/Domain/Entity/Murid.php` - disable incrementing, set keyType, add boot() generateId()
- [x] 4. Update `app/Http/Repository/MuridRepository.php` - add getNextCounterForYear(), findById(string), add findByKelas()
- [x] 5. Update `app/Domain/Entity/Presensi.php` and `app/Domain/Entity/LaporanKegiatan.php` - cast id_murid to string
- [x] 6. Update `app/Http/Repository/PresensiRepository.php` - string type hints
- [x] 7. Update `app/Http/Repository/LaporanKegiatanRepository.php` - string type hints
- [x] 8. Update `app/Http/Service/MuridService.php` - string type hints
- [x] 9. Update `app/Http/Service/PresensiService.php` - string type hints
- [x] 10. Update `app/Http/Service/LaporanKegiatanService.php` - string type hints
- [x] 11. Update `app/Http/Controllers/MuridController.php` - string type hints
- [x] 12. Update `app/Http/Controllers/PresensiController.php` - string type hints
- [x] 13. Update `app/Http/Controllers/LaporanKegiatanController.php` - string type hints
- [x] 14. Update `database/seeders/DatabaseSeeder.php` - use prefixed IDs

### Frontend
- [x] 15. Update blade files - remove parseInt() on id_murid in laporan-murid.blade.php

---

## ✅ Task 2: Balasan Comment Deletion (Frontend)

- [x] 16. Update `resources/views/laporan/index.blade.php` - add delete button per balasan + JS functions

