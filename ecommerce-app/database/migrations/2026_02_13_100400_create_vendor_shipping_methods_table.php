<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendor_shipping_methods', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->index();
            $table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('code')->index();
            $table->decimal('base_rate', 10, 2)->default(0);
            $table->decimal('extra_rate', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->json('rules')->nullable();
            $table->timestamps();

            $table->unique(['vendor_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_shipping_methods');
    }
};
