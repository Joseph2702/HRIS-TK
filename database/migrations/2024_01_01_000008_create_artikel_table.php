<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('artikel', function (Blueprint $table) {
            $table->increments('id_artikel');
            $table->unsignedInteger('id_user')->nullable();
            $table->string('judul_artikel', 255);
            $table->text('gambar_artikel')->nullable();
            $table->text('konten_artikel')->nullable();
            $table->string('status_artikel', 20)->default('published');
            $table->timestamp('tanggal_publish')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('id_user')->references('id_user')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('artikel');
    }
};
