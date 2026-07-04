<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_log', function (Blueprint $table) {
            $table->increments('id_log');
            $table->unsignedInteger('id_user')->nullable();
            $table->string('modul', 100)->nullable();
            $table->string('aktivitas', 100)->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamp('tanggal_log')->useCurrent();

            $table->foreign('id_user')->references('id_user')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_log');
    }
};
