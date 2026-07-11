<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ulasan_layanan', function (Blueprint $table) {
            $table->increments('id_ulasan');

            $table->unsignedInteger('id_artikel');
            $table->unsignedInteger('id_user');

            $table->unsignedTinyInteger('rating'); // 1-5
            $table->text('isi_ulasan')->nullable();

            $table->timestamp('created_at')->useCurrent();

            $table->unique(['id_artikel', 'id_user']);

            $table->foreign('id_artikel')->references('id_artikel')->on('artikel')->onDelete('cascade');
            $table->foreign('id_user')->references('id_user')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ulasan_layanan');
    }
};
