<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('notifikasi', function (Blueprint $table) {
            $table->increments('id_notif');
            $table->unsignedInteger('id_user');        // penerima
            $table->string('judul', 200);
            $table->text('pesan')->nullable();
            $table->string('tipe', 50);                // laporan | balasan | artikel
            $table->unsignedInteger('id_referensi')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('created_at')->useCurrent();
            $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
        });
    }
    public function down(): void { Schema::dropIfExists('notifikasi'); }
};
