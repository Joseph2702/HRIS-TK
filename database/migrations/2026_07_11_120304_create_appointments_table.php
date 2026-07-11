<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id('id_appointment');

            // Relasi utama
            $table->foreignId('id_orang_tua')->constrained('orang_tua', 'id_orang_tua')->cascadeOnDelete();
            $table->foreignId('id_murid')->constrained('murid', 'id_murid')->cascadeOnDelete();

            // Opsional: konsultasi bisa terkait jadwal kelas tertentu
            $table->foreignId('id_jadwal')->nullable()->constrained('jadwal_kelas', 'id_jadwal')->nullOnDelete();

            // Rentang waktu konsultasi (indikator dihitung di periode ini)
            $table->date('from_date');
            $table->date('to_date');

            // Rule #3 (indikator <= MB dalam periode): simpan nilai & catatan rule
            $table->string('indikator_threshold_rule')->nullable();
            $table->text('reason')->nullable();

            // Status proses
            // pending => menunggu approve admin
            // approved => disetujui (menunggu/ada assign guru)
            // rejected => ditolak
            // completed => selesai (opsional di masa depan)
            $table->string('status')->default('pending');

            // Assignment
            $table->foreignId('assigned_guru_id')->nullable()->constrained('guru', 'id_guru')->nullOnDelete();

            // Approved oleh admin (user id)
            $table->foreignId('approved_by')->nullable()->constrained('users', 'id_user')->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
