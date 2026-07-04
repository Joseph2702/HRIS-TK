<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('balasan_laporan', function (Blueprint $table) {
            $table->increments('id_balasan');
            $table->unsignedInteger('id_laporan')->nullable();
            $table->unsignedInteger('id_user')->nullable();
            $table->text('isi_balasan');
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('id_laporan')->references('id_laporan')->on('laporan_kegiatan')->onDelete('cascade');
            $table->foreign('id_user')->references('id_user')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('balasan_laporan');
    }
};
