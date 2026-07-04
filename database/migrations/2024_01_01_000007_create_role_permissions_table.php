<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->unsignedInteger('id_role');
            $table->unsignedInteger('id_permission');
            $table->primary(['id_role', 'id_permission']);

            $table->foreign('id_role')->references('id_role')->on('roles')->onDelete('cascade');
            $table->foreign('id_permission')->references('id_permission')->on('permissions')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_permissions');
    }
};
