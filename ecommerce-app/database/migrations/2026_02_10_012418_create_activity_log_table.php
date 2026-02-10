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
        Schema::create('activity_log', function (Blueprint $table) {
            $table->id(); // Primary key auto-incremental

            // Campos específicos de la tabla activity_log
            $table->string('log_name')->nullable()->index();
            $table->text('description');
            $table->morphs('subject'); // subject_type, subject_id
            $table->morphs('causer'); // causer_type, causer_id
            $table->json('properties')->nullable();

            // Campos Auditables Obligatorios (sin softDeletes)
            $table->timestamps(); // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_log');
    }
};
