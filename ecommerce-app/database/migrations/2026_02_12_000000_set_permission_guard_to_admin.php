<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $roles = config('permission.table_names.roles');
        $permissions = config('permission.table_names.permissions');

        if (\Illuminate\Support\Facades\Schema::hasTable($roles)) {
            DB::table($roles)->where('guard_name', '!=', 'admin')->update(['guard_name' => 'admin']);
        }
        if (\Illuminate\Support\Facades\Schema::hasTable($permissions)) {
            DB::table($permissions)->where('guard_name', '!=', 'admin')->update(['guard_name' => 'admin']);
        }
    }

    public function down(): void
    {
        // No revertir para no romper datos
    }
};
