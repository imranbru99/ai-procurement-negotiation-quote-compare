<?php

namespace App\Modules\contracts\Console;

use App\Models\Contract;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ContractRenewalAlertCommand extends Command
{
    protected $signature = 'contracts:renewal-alerts';

    protected $description = 'Notify about contracts approaching renewal/end date';

    public function handle(): int
    {
        Contract::query()
            ->withoutGlobalScopes()
            ->whereNotNull('ends_at')
            ->where('status', 'active')
            ->chunkById(50, function ($contracts): void {
                foreach ($contracts as $contract) {
                    $days = $contract->renewal_alert_days ?? 30;
                    if ($contract->ends_at && $contract->ends_at->lte(now()->addDays($days))) {
                        Log::info('Contract renewal alert', [
                            'contract_id' => $contract->id,
                            'ends_at' => $contract->ends_at,
                        ]);
                    }
                }
            });

        $this->info('Renewal alerts processed.');

        return self::SUCCESS;
    }
}
