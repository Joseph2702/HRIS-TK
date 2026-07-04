<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_kelas', function (Blueprint $table) {
            $table->increments('id_jadwal');
            $table->unsignedInteger('id_kelas')->nullable();
            $table->unsignedInteger('id_guru')->nullable();
            $table->date('tanggal');
            $table->time('jam_mulai')->nullable();
            $table->time('jam_selesai')->nullable();
            $table->string('topik', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('id_kelas')->references('id_kelas')->on('kelas')->nullOnDelete();
            $table->foreign('id_guru')->references('id_guru')->on('guru')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_kelas');
    }
};
