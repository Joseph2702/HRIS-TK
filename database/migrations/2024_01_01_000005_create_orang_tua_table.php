<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orang_tua', function (Blueprint $table) {
            $table->increments('id_orang_tua');
            $table->unsignedInteger('id_user')->nullable();
            $table->string('pekerjaan', 100)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orang_tua');
    }
};
