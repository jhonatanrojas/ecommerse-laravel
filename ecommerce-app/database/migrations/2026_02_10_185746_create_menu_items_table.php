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
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('menu_id')->constrained('menus')->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('menu_items')->onDelete('cascade');
            $table->string('label', 100);
            $table->string('url', 500)->nullable();
            $table->string('route_name')->nullable();
            $table->json('route_params')->nullable();
            $table->enum('type', ['internal', 'external', 'route', 'category', 'custom'])->default('internal');
            $table->enum('target', ['_self', '_blank'])->default('_self')->nullable();
            $table->string('icon')->nullable();
            $table->string('css_classes')->nullable();
            $table->string('badge_text')->nullable();
            $table->string('badge_color')->nullable();
            $table->integer('order')->default(0);
            $table->integer('depth')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('is_featured')->default(false);
            $table->boolean('open_in_new_tab')->default(false);
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['menu_id', 'order']);
            $table->index(['menu_id', 'parent_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
