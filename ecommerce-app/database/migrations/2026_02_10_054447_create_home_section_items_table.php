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
        Schema::create('home_section_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('home_section_id')
                ->constrained('home_sections')
                ->onDelete('cascade');
            $table->string('itemable_type')->nullable();
            $table->unsignedBigInteger('itemable_id')->nullable();
            $table->unsignedInteger('display_order')->default(0);
            $table->json('configuration')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['home_section_id', 'display_order']);
            $table->index(['itemable_type', 'itemable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_section_items');
    }
};
