<?php

namespace App\Services;

use App\Models\Item;
use App\Models\ItemUsage;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class StockService
{
    public function use(int $itemId, int $qty, ?int $callId = null, ?string $note = null): Item
    {
        return DB::transaction(function () use ($itemId, $qty, $callId, $note) {
            $item = Item::lockForUpdate()->findOrFail($itemId);

            if ($item->stock < $qty) {
                throw new RuntimeException("Stok {$item->name} tidak cukup (sisa {$item->stock}).");
            }

            $item->decrement('stock', $qty);

            ItemUsage::create([
                'item_id'  => $item->id,
                'call_id'  => $callId,
                'quantity' => $qty,
                'note'     => $note,
            ]);

            return $item->refresh();
        });
    }

    public function add(int $itemId, int $qty, ?string $note = null): Item
    {
        return DB::transaction(function () use ($itemId, $qty) {
            $item = Item::lockForUpdate()->findOrFail($itemId);
            $item->increment('stock', $qty);

            return $item->refresh();
        });
    }
}