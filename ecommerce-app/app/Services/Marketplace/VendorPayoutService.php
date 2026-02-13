<?php

namespace App\Services\Marketplace;

use App\Models\Vendor;
use App\Models\VendorOrder;
use App\Models\VendorPayout;
use App\Models\StoreSetting;
use App\Services\Marketplace\Contracts\VendorPayoutGatewayInterface;
use App\Services\Marketplace\Payouts\ManualPayoutGateway;
use App\Services\Marketplace\Payouts\PayPalPayoutGateway;
use App\Services\Marketplace\Payouts\StripeConnectPayoutGateway;
use Illuminate\Support\Facades\DB;

class VendorPayoutService
{
    public function createPendingPayout(Vendor $vendor, ?float $amount = null): ?VendorPayout
    {
        $query = VendorOrder::query()
            ->where('vendor_id', $vendor->id)
            ->where('payout_status', 'pending')
            ->whereHas('order', fn ($q) => $q->where('payment_status', 'paid'));

        $available = (float) $query->sum('vendor_earnings');

        if ($available <= 0) {
            return null;
        }

        $payoutAmount = $amount !== null ? min($amount, $available) : $available;

        return VendorPayout::query()->create([
            'vendor_id' => $vendor->id,
            'amount' => round($payoutAmount, 2),
            'payout_method' => $vendor->payout_method,
            'status' => 'pending',
            'provider' => $this->detectProvider($vendor),
        ]);
    }

    public function processPayout(VendorPayout $payout): VendorPayout
    {
        return DB::transaction(function () use ($payout) {
            $payout->update(['status' => 'processing']);

            $provider = $payout->provider ?: 'manual';
            $response = $this->gateway($provider)->payout($payout);

            if (! ($response['success'] ?? false)) {
                $payout->update([
                    'status' => 'failed',
                    'meta' => $response['meta'] ?? [],
                ]);

                return $payout->fresh();
            }

            $payout->update([
                'status' => 'completed',
                'transaction_reference' => $response['transaction_reference'] ?? null,
                'meta' => $response['meta'] ?? [],
                'processed_at' => now(),
            ]);

            VendorOrder::query()
                ->where('vendor_id', $payout->vendor_id)
                ->where('payout_status', 'pending')
                ->whereHas('order', fn ($q) => $q->where('payment_status', 'paid'))
                ->update(['payout_status' => 'paid']);

            return $payout->fresh();
        });
    }

    public function processAutomaticPayouts(): int
    {
        $enabled = (bool) (StoreSetting::query()->value('enable_automatic_payouts') ?? false);
        if (! $enabled) {
            return 0;
        }

        $vendors = Vendor::query()
            ->where('status', 'approved')
            ->whereIn('payout_cycle', ['weekly', 'monthly'])
            ->get();

        $count = 0;

        foreach ($vendors as $vendor) {
            $eligible = match ($vendor->payout_cycle) {
                'weekly' => now()->isSunday(),
                'monthly' => now()->isLastOfMonth(),
                default => false,
            };

            if (! $eligible) {
                continue;
            }

            $payout = $this->createPendingPayout($vendor);
            if (! $payout) {
                continue;
            }

            $this->processPayout($payout);
            $count++;
        }

        return $count;
    }

    protected function detectProvider(Vendor $vendor): string
    {
        $provider = strtolower((string) data_get($vendor->payout_method, 'provider', 'manual'));

        if (in_array($provider, ['stripe', 'stripe_connect'], true)) {
            return 'stripe_connect';
        }

        if (in_array($provider, ['paypal', 'paypal_payouts'], true)) {
            return 'paypal_payouts';
        }

        return 'manual';
    }

    protected function gateway(string $provider): VendorPayoutGatewayInterface
    {
        return match ($provider) {
            'stripe_connect' => app(StripeConnectPayoutGateway::class),
            'paypal_payouts' => app(PayPalPayoutGateway::class),
            default => app(ManualPayoutGateway::class),
        };
    }
}
