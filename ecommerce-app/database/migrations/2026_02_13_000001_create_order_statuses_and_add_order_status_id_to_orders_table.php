<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('color')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('is_default')->default(false)->index();
            $table->timestamps();
        });

        DB::table('order_statuses')->insert([
            [
                'name' => 'Pending',
                'slug' => 'pending',
                'color' => '#F59E0B',
                'is_active' => true,
                'is_default' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Processing',
                'slug' => 'processing',
                'color' => '#3B82F6',
                'is_active' => true,
                'is_default' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Shipped',
                'slug' => 'shipped',
                'color' => '#8B5CF6',
                'is_active' => true,
                'is_default' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Delivered',
                'slug' => 'delivered',
                'color' => '#10B981',
                'is_active' => true,
                'is_default' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Cancelled',
                'slug' => 'cancelled',
                'color' => '#EF4444',
                'is_active' => true,
                'is_default' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('order_status_id')
                ->nullable()
                ->after('status')
                ->constrained('order_statuses')
                ->restrictOnDelete();
        });

        $statusMap = DB::table('order_statuses')->pluck('id', 'slug')->all();
        $defaultStatusId = DB::table('order_statuses')->where('is_default', true)->value('id');

        DB::table('orders')
            ->select(['id', 'status'])
            ->orderBy('id')
            ->chunkById(200, function ($orders) use ($statusMap, $defaultStatusId) {
                foreach ($orders as $order) {
                    $statusSlug = (string) $order->status;
                    $mappedId = $statusMap[$statusSlug] ?? $defaultStatusId;

                    if ($mappedId) {
                        DB::table('orders')
                            ->where('id', $order->id)
                            ->update(['order_status_id' => $mappedId]);
                    }
                }
            });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('order_status_id');
        });

        Schema::dropIfExists('order_statuses');
    }
};
