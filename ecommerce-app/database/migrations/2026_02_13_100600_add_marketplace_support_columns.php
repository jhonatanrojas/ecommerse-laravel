<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->decimal('commission_rate', 5, 2)->nullable()->after('is_active');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('vendor_id')->nullable()->after('order_id')->constrained()->nullOnDelete();
            $table->index(['order_id', 'vendor_id']);
        });

        Schema::table('store_settings', function (Blueprint $table) {
            $table->decimal('marketplace_commission_rate', 5, 2)->default(10)->after('tax_rate');
            $table->boolean('auto_approve_vendors')->default(false)->after('allow_guest_checkout');
            $table->boolean('auto_approve_vendor_products')->default(false)->after('auto_approve_vendors');
            $table->boolean('enable_automatic_payouts')->default(false)->after('auto_approve_vendor_products');
        });
    }

    public function down(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {
            $table->dropColumn([
                'marketplace_commission_rate',
                'auto_approve_vendors',
                'auto_approve_vendor_products',
                'enable_automatic_payouts',
            ]);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['vendor_id']);
            $table->dropIndex(['order_id', 'vendor_id']);
            $table->dropColumn('vendor_id');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('commission_rate');
        });
    }
};
