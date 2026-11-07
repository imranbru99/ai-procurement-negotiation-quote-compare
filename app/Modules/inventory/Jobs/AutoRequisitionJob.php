<?php

namespace App\Modules\inventory\Jobs;

use App\Models\StockItem;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AutoRequisitionJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function handle(): void
    {
        StockItem::query()
            ->withoutGlobalScopes()
            ->whereColumn('qty_on_hand', '<=', 'reorder_point')
            ->where('reorder_qty', '>', 0)
            ->chunkById(100, function ($items): void {
                foreach ($items as $item) {
                    Log::info('Auto-requisition triggered', [
                        'stock_item_id' => $item->id,
                        'reorder_qty' => $item->reorder_qty,
                    ]);
                }
            });
    }
}
