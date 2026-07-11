<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ulasan_layanan', function (Blueprint $table) {
            // Ubah constraint unique: 1 user hanya 1 ulasan global
            // Kita drop index unique lama dan buat unique baru pada id_user.
            // Nama index default Laravel biasanya sesuai aturan: {table}_{column1}_{column2}_unique
            // Namun karena implementasi sebelumnya pakai $table->unique(['id_artikel','id_user'])
            // kita coba pakai nama defaultnya. Jika berbeda di DB, migrasi ini perlu penyesuaian.
            $table->dropUnique(['id_artikel', 'id_user']);
            $table->unique(['id_user']);

            // id_artikel tidak dipakai untuk ulasan global
            $table->unsignedInteger('id_artikel')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('ulasan_layanan', function (Blueprint $table) {
            // Balik unique global ke unique per layanan
            $table->dropUnique(['id_user']);
            $table->unique(['id_artikel', 'id_user']);

            $table->unsignedInteger('id_artikel')->nullable(false)->change();
        });
    }
};
