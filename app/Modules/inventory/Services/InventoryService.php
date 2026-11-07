<?php

namespace App\Modules\inventory\Services;

use App\Models\GoodsReceiptLine;
use App\Models\StockItem;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    public function receiveFromGrnLine(GoodsReceiptLine $line, StockItem $stockItem, ?User $user = null): StockItem
    {
        return DB::transaction(function () use ($line, $stockItem, $user) {
            $qty = (float) $line->qty_accepted;

            $stockItem->increment('qty_on_hand', $qty);

            StockMovement::query()->create([
                'stock_item_id' => $stockItem->id,
                'type' => 'in',
                'qty' => $qty,
                'reference_type' => GoodsReceiptLine::class,
                'reference_id' => $line->id,
                'user_id' => $user?->id,
            ]);

            return $stockItem->fresh();
        });
    }
}
