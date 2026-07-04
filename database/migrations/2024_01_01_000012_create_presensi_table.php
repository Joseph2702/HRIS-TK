<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('presensi', function (Blueprint $table) {
            $table->increments('id_presensi');
            $table->unsignedInteger('id_jadwal')->nullable();
            $table->unsignedInteger('id_murid')->nullable();
            $table->string('status_kehadiran', 20);
            $table->text('keterangan')->nullable();
            $table->unsignedInteger('dicatat_oleh')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->unique(['id_jadwal', 'id_murid']);

            $table->foreign('id_jadwal')->references('id_jadwal')->on('jadwal_kelas')->nullOnDelete();
            $table->foreign('id_murid')->references('id_murid')->on('murid')->nullOnDelete();
            $table->foreign('dicatat_oleh')->references('id_user')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('presensi');
    }
};
