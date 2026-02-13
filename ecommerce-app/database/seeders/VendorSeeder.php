<?php

namespace Database\Seeders;

use App\Models\StoreSetting;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class VendorSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure store settings exists for marketplace defaults
        StoreSetting::query()->firstOrCreate([], [
            'store_name' => 'Mi Tienda',
            'currency' => 'USD',
            'currency_symbol' => '$',
            'tax_rate' => 0,
            'marketplace_commission_rate' => 10,
        ]);

        $vendors = [
            [
                'user' => [
                    'name' => 'Vendor Tech',
                    'email' => 'vendor.tech@example.com',
                    'password' => Hash::make('password'),
                    'is_active' => true,
                ],
                'vendor' => [
                    'business_name' => 'Tech Seller LLC',
                    'document' => 'J-10000000-1',
                    'phone' => '+1 555 100 0001',
                    'email' => 'vendor.tech@example.com',
                    'address' => '100 Market St, Miami, FL',
                    'status' => 'approved',
                    'commission_rate' => 12,
                    'payout_cycle' => 'weekly',
                    'payout_method' => [
                        'provider' => 'manual',
                        'account' => 'US00TECH0001',
                        'beneficiary' => 'Tech Seller LLC',
                    ],
                    'approved_at' => now()->subDays(10),
                ],
            ],
            [
                'user' => [
                    'name' => 'Vendor Fashion',
                    'email' => 'vendor.fashion@example.com',
                    'password' => Hash::make('password'),
                    'is_active' => true,
                ],
                'vendor' => [
                    'business_name' => 'Fashion Hub Inc',
                    'document' => 'J-10000000-2',
                    'phone' => '+1 555 100 0002',
                    'email' => 'vendor.fashion@example.com',
                    'address' => '220 Fifth Ave, New York, NY',
                    'status' => 'approved',
                    'commission_rate' => 9,
                    'payout_cycle' => 'monthly',
                    'payout_method' => [
                        'provider' => 'manual',
                        'account' => 'US00FASH0002',
                        'beneficiary' => 'Fashion Hub Inc',
                    ],
                    'approved_at' => now()->subDays(8),
                ],
            ],
            [
                'user' => [
                    'name' => 'Vendor Pending',
                    'email' => 'vendor.pending@example.com',
                    'password' => Hash::make('password'),
                    'is_active' => true,
                ],
                'vendor' => [
                    'business_name' => 'Pending Supplies Co',
                    'document' => 'J-10000000-3',
                    'phone' => '+1 555 100 0003',
                    'email' => 'vendor.pending@example.com',
                    'address' => '10 Bay Rd, Tampa, FL',
                    'status' => 'pending',
                    'commission_rate' => null,
                    'payout_cycle' => 'manual',
                    'payout_method' => [
                        'provider' => 'manual',
                        'account' => 'US00PEND0003',
                        'beneficiary' => 'Pending Supplies Co',
                    ],
                    'approved_at' => null,
                ],
            ],
            [
                'user' => [
                    'name' => 'Vendor Suspended',
                    'email' => 'vendor.suspended@example.com',
                    'password' => Hash::make('password'),
                    'is_active' => true,
                ],
                'vendor' => [
                    'business_name' => 'Suspended Store Ltd',
                    'document' => 'J-10000000-4',
                    'phone' => '+1 555 100 0004',
                    'email' => 'vendor.suspended@example.com',
                    'address' => '500 Pine St, Seattle, WA',
                    'status' => 'suspended',
                    'commission_rate' => 15,
                    'payout_cycle' => 'manual',
                    'payout_method' => [
                        'provider' => 'manual',
                        'account' => 'US00SUSP0004',
                        'beneficiary' => 'Suspended Store Ltd',
                    ],
                    'approved_at' => null,
                    'rejection_reason' => 'Incumplimiento de políticas',
                ],
            ],
        ];

        foreach ($vendors as $item) {
            $user = User::query()->firstOrCreate(
                ['email' => $item['user']['email']],
                $item['user']
            );

            Vendor::query()->updateOrCreate(
                ['user_id' => $user->id],
                [...$item['vendor'], 'user_id' => $user->id]
            );
        }

        $this->command?->info('Vendedores marketplace creados/actualizados.');
    }
}
