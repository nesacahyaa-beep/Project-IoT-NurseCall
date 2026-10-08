<?php

namespace App\Services;

use App\Models\{Item, ItemUsage};
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StockService
{
    public function use(int $itemId, int $qty, ?int $callId = null, ?string $note = null): Item
    {
        return DB::transaction(function () use ($itemId, $qty, $callId, $note) {
            $item = Item::lockForUpdate()->findOrFail($itemId);

            if ($item->stock < $qty) {
                throw ValidationException::withMessages(['quantity' => "Stok {$item->name} tidak cukup."]);
            }
            $item->decrement('stock', $qty);
            ItemUsage::create([
                'item_id' => $item->id, 'call_id' => $callId,
                'type' => 'out', 'quantity' => $qty, 'note' => $note,
            ]);
            return $item->refresh(); // cek $item->needs_reorder
        });
    }

    public function add(int $itemId, int $qty, ?string $note = null): Item
    {
        return DB::transaction(function () use ($itemId, $qty, $note) {
            $item = Item::lockForUpdate()->findOrFail($itemId);
            $item->increment('stock', $qty);
            ItemUsage::create(['item_id' => $item->id, 'type' => 'in', 'quantity' => $qty, 'note' => $note]);
            return $item->refresh();
        });
    }
}