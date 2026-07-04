<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('CREATE INDEX IF NOT EXISTS idx_presensi_jadwal ON presensi(id_jadwal)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_presensi_murid ON presensi(id_murid)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_laporan_murid ON laporan_kegiatan(id_murid)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_laporan_jadwal ON laporan_kegiatan(id_jadwal)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_laporan_guru ON laporan_kegiatan(id_guru)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_murid_orang_tua ON murid(id_orang_tua)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_murid_kelas ON murid(id_kelas)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_jadwal_guru ON jadwal_kelas(id_guru)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_jadwal_kelas ON jadwal_kelas(id_kelas)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_jadwal_tanggal ON jadwal_kelas(tanggal)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_artikel_user ON artikel(id_user)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_balasan_laporan ON balasan_laporan(id_laporan)');
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS idx_presensi_jadwal');
        DB::statement('DROP INDEX IF EXISTS idx_presensi_murid');
        DB::statement('DROP INDEX IF EXISTS idx_laporan_murid');
        DB::statement('DROP INDEX IF EXISTS idx_laporan_jadwal');
        DB::statement('DROP INDEX IF EXISTS idx_laporan_guru');
        DB::statement('DROP INDEX IF EXISTS idx_murid_orang_tua');
        DB::statement('DROP INDEX IF EXISTS idx_murid_kelas');
        DB::statement('DROP INDEX IF EXISTS idx_jadwal_guru');
        DB::statement('DROP INDEX IF EXISTS idx_jadwal_kelas');
        DB::statement('DROP INDEX IF EXISTS idx_jadwal_tanggal');
        DB::statement('DROP INDEX IF EXISTS idx_artikel_user');
        DB::statement('DROP INDEX IF EXISTS idx_balasan_laporan');
    }
};
