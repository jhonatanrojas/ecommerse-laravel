<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendor_products', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->index();
            $table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('is_approved')->default(false)->index();
            $table->text('moderation_notes')->nullable();
            $table->json('moderation_history')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->unique(['vendor_id', 'product_id']);
            $table->index(['vendor_id', 'is_active', 'is_approved']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_products');
    }
};
