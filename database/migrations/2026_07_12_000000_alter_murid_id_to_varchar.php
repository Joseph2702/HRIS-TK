<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop foreign keys that reference murid.id_murid
        $this->dropForeignKeys();

        // Step 1: Change all referencing columns to VARCHAR first (before updating data)
        DB::statement('ALTER TABLE presensi ALTER COLUMN id_murid TYPE VARCHAR(20)');
        DB::statement('ALTER TABLE laporan_kegiatan ALTER COLUMN id_murid TYPE VARCHAR(20)');
        DB::statement('ALTER TABLE appointments ALTER COLUMN id_murid TYPE VARCHAR(20)');

        // Step 2: Change murid.id_murid from int to varchar(20)
        DB::statement('ALTER TABLE murid ALTER COLUMN id_murid DROP DEFAULT');
        DB::statement('ALTER TABLE murid ALTER COLUMN id_murid DROP IDENTITY IF EXISTS');
        DB::statement('ALTER TABLE murid ALTER COLUMN id_murid TYPE VARCHAR(20)');
        DB::statement('ALTER TABLE murid ALTER COLUMN id_murid SET NOT NULL');

        // Step 3: Update existing data with year prefix
        $prefix = date('Y');
        $murids = DB::table('murid')->orderBy('id_murid')->get();
        foreach ($murids as $murid) {
            $newId = $prefix . '-' . str_pad($murid->id_murid, 3, '0', STR_PAD_LEFT);
            DB::table('murid')->where('id_murid', $murid->id_murid)->update(['id_murid' => $newId]);
        }

        $presensis = DB::table('presensi')->get();
        foreach ($presensis as $presensi) {
            if ($presensi->id_murid) {
                $newId = $prefix . '-' . str_pad($presensi->id_murid, 3, '0', STR_PAD_LEFT);
                DB::table('presensi')->where('id_presensi', $presensi->id_presensi)->update(['id_murid' => $newId]);
            }
        }

        $laporans = DB::table('laporan_kegiatan')->get();
        foreach ($laporans as $laporan) {
            if ($laporan->id_murid) {
                $newId = $prefix . '-' . str_pad($laporan->id_murid, 3, '0', STR_PAD_LEFT);
                DB::table('laporan_kegiatan')->where('id_laporan', $laporan->id_laporan)->update(['id_murid' => $newId]);
            }
        }

        $appointments = DB::table('appointments')->get();
        foreach ($appointments as $appointment) {
            if ($appointment->id_murid) {
                $newId = $prefix . '-' . str_pad($appointment->id_murid, 3, '0', STR_PAD_LEFT);
                DB::table('appointments')->where('id_appointment', $appointment->id_appointment)->update(['id_murid' => $newId]);
            }
        }

        // Step 4: Re-add foreign keys
        Schema::table('presensi', function (Blueprint $table) {
            $table->foreign('id_murid')->references('id_murid')->on('murid')->nullOnDelete();
        });
        Schema::table('laporan_kegiatan', function (Blueprint $table) {
            $table->foreign('id_murid')->references('id_murid')->on('murid')->nullOnDelete();
        });
        Schema::table('appointments', function (Blueprint $table) {
            $table->foreign('id_murid')->references('id_murid')->on('murid')->cascadeOnDelete();
        });
    }

    private function dropForeignKeys(): void
    {
        Schema::table('presensi', function (Blueprint $table) {
            $table->dropForeign(['id_murid']);
        });
        Schema::table('laporan_kegiatan', function (Blueprint $table) {
            $table->dropForeign(['id_murid']);
        });
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropForeign(['id_murid']);
        });
    }

    public function down(): void
    {
        $this->dropForeignKeys();

        // Convert back: remove prefix from id_murid
        $murids = DB::table('murid')->get();
        foreach ($murids as $murid) {
            $parts = explode('-', $murid->id_murid);
            $counter = isset($parts[1]) ? (int)$parts[1] : 0;
            if ($counter > 0) {
                DB::table('murid')->where('id_murid', $murid->id_murid)->update(['id_murid' => (string)$counter]);
            }
        }

        $presensis = DB::table('presensi')->get();
        foreach ($presensis as $presensi) {
            if ($presensi->id_murid) {
                $parts = explode('-', $presensi->id_murid);
                $counter = isset($parts[1]) ? (int)$parts[1] : 0;
                if ($counter > 0) {
                    DB::table('presensi')->where('id_presensi', $presensi->id_presensi)->update(['id_murid' => (string)$counter]);
                }
            }
        }

        $laporans = DB::table('laporan_kegiatan')->get();
        foreach ($laporans as $laporan) {
            if ($laporan->id_murid) {
                $parts = explode('-', $laporan->id_murid);
                $counter = isset($parts[1]) ? (int)$parts[1] : 0;
                if ($counter > 0) {
                    DB::table('laporan_kegiatan')->where('id_laporan', $laporan->id_laporan)->update(['id_murid' => (string)$counter]);
                }
            }
        }

        $appointments = DB::table('appointments')->get();
        foreach ($appointments as $appointment) {
            if ($appointment->id_murid) {
                $parts = explode('-', $appointment->id_murid);
                $counter = isset($parts[1]) ? (int)$parts[1] : 0;
                if ($counter > 0) {
                    DB::table('appointments')->where('id_appointment', $appointment->id_appointment)->update(['id_murid' => (string)$counter]);
                }
            }
        }

        // Change back to integer
        DB::statement('ALTER TABLE murid ALTER COLUMN id_murid TYPE INTEGER USING id_murid::integer');
        DB::statement('ALTER TABLE murid ALTER COLUMN id_murid SET NOT NULL');
        DB::statement("CREATE SEQUENCE IF NOT EXISTS murid_id_murid_seq OWNED BY murid.id_murid");
        DB::statement("ALTER TABLE murid ALTER COLUMN id_murid SET DEFAULT nextval('murid_id_murid_seq')");
        DB::statement("SELECT setval('murid_id_murid_seq', COALESCE((SELECT MAX(id_murid) FROM murid), 1))");

        DB::statement('ALTER TABLE presensi ALTER COLUMN id_murid TYPE INTEGER USING id_murid::integer');
        DB::statement('ALTER TABLE laporan_kegiatan ALTER COLUMN id_murid TYPE INTEGER USING id_murid::integer');
        DB::statement('ALTER TABLE appointments ALTER COLUMN id_murid TYPE BIGINT USING id_murid::bigint');

        // Re-add foreign keys
        Schema::table('presensi', function (Blueprint $table) {
            $table->foreign('id_murid')->references('id_murid')->on('murid')->nullOnDelete();
        });
        Schema::table('laporan_kegiatan', function (Blueprint $table) {
            $table->foreign('id_murid')->references('id_murid')->on('murid')->nullOnDelete();
        });
        Schema::table('appointments', function (Blueprint $table) {
            $table->foreign('id_murid')->references('id_murid')->on('murid')->cascadeOnDelete();
        });
    }
};
