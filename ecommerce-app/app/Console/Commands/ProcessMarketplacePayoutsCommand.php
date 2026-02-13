<?php

namespace App\Console\Commands;

use App\Services\Marketplace\VendorPayoutService;
use Illuminate\Console\Command;

class ProcessMarketplacePayoutsCommand extends Command
{
    protected $signature = 'marketplace:payouts:auto';

    protected $description = 'Procesa liquidaciones automáticas de vendedores marketplace';

    public function __construct(
        protected VendorPayoutService $payoutService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $count = $this->payoutService->processAutomaticPayouts();

        $this->info("Liquidaciones automáticas procesadas: {$count}");

        return self::SUCCESS;
    }
}
