<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan_kegiatan', function (Blueprint $table) {
            $table->increments('id_laporan');
            $table->unsignedInteger('id_jadwal')->nullable();
            $table->unsignedInteger('id_murid')->nullable();
            $table->unsignedInteger('id_guru')->nullable();
            $table->string('judul_laporan', 255);
            $table->text('isi_laporan')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('id_jadwal')->references('id_jadwal')->on('jadwal_kelas')->nullOnDelete();
            $table->foreign('id_murid')->references('id_murid')->on('murid')->nullOnDelete();
            $table->foreign('id_guru')->references('id_guru')->on('guru')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_kegiatan');
    }
};
