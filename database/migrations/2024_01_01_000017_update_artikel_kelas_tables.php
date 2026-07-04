<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('artikel', function (Blueprint $table) {
            $table->text('gambar_artikel_2')->nullable()->after('gambar_artikel');
            $table->string('tipe', 30)->default('tentang_sekolah')->after('status_artikel');
            // tipe: tentang_sekolah | layanan_sekolah
        });
        Schema::table('kelas', function (Blueprint $table) {
            $table->integer('kapasitas')->default(20)->after('deskripsi');
        });
    }
    public function down(): void {
        Schema::table('artikel', function (Blueprint $table) {
            $table->dropColumn(['gambar_artikel_2', 'tipe']);
        });
        Schema::table('kelas', function (Blueprint $table) {
            $table->dropColumn('kapasitas');
        });
    }
};
