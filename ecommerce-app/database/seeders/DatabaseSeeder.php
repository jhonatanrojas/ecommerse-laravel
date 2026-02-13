<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class ,
            UserModuleSeeder::class ,
            AdminUserSeeder::class ,
            CategorySeeder::class ,
            ProductSeeder::class ,
            OrderStatusSeeder::class,
            ShippingStatusSeeder::class,
            PaymentMethodSeeder::class,
            DefaultPagesSeeder::class,
            MenuSeeder::class ,
            EnsureTiendaMenuItemSeeder::class,
            HomeSectionSeeder::class ,
        ]);
    }
}
