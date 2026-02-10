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
        Schema::create('product_views', function (Blueprint $table) {
            $table->id(); // Primary key auto-incremental

            // Foreign Keys
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');

            // Campos específicos de la tabla product_views
            $table->string('session_id')->nullable()->index();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('viewed_at')->useCurrent()->index(); // Usar useCurrent para establecer la fecha por defecto
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_views');
    }
};
