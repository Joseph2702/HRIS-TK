<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('murid', function (Blueprint $table) {
            $table->increments('id_murid');
            $table->unsignedInteger('id_orang_tua')->nullable();
            $table->unsignedInteger('id_kelas')->nullable();
            $table->string('nama_murid', 100);
            $table->date('tanggal_lahir')->nullable();
            $table->string('jenis_kelamin', 10)->nullable();
            $table->text('foto_murid')->nullable();
            $table->string('status_murid', 20)->default('aktif');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('id_orang_tua')->references('id_orang_tua')->on('orang_tua')->nullOnDelete();
            $table->foreign('id_kelas')->references('id_kelas')->on('kelas')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('murid');
    }
};
