<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('laporan_kegiatan', function (Blueprint $table) {
            $table->string('indikator', 10)->nullable()->after('isi_laporan');
            $table->text('indikator_catatan')->nullable()->after('indikator');
        });
    }

    public function down(): void
    {
        Schema::table('laporan_kegiatan', function (Blueprint $table) {
            $table->dropColumn(['indikator', 'indikator_catatan']);
        });
    }
};
