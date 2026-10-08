<?php

namespace App\Services;

use App\Models\{Call, Room};
use Illuminate\Support\Facades\DB;

class CallService
{
    public function __construct(private StockService $stock) {}

    public function receive(Room $room, string $level): Call
    {
        return DB::transaction(function () use ($room, $level) {
            $call = $room->calls()->active()->lockForUpdate()->first();

            if (! $call) {
                return $room->calls()->create([
                    'level' => $level, 'status' => 'waiting', 'called_at' => now(),
                ]);
            }
            // Tombol ditekan berulang: jangan buat duplikat, tapi naikkan ke darurat
            if ($level === 'emergency' && $call->level !== 'emergency') {
                $call->update(['level' => 'emergency']);
            }
            return $call;
        });
    }

    public function accept(Call $call, ?int $userId = null): Call
    {
        if ($call->status !== 'waiting') return $call;

        $now = now();
        $call->update([
            'status'           => 'accepted',
            'accepted_at'      => $now,
            'accepted_by'      => $userId,
            'response_seconds' => (int) $call->called_at->diffInSeconds($now),
        ]);
        return $call;
    }

    /** @param array<int,array{item_id:int,quantity:int}> $items */
    public function complete(Call $call, array $items = [], ?int $userId = null): Call
    {
        return DB::transaction(function () use ($call, $items, $userId) {
            if ($call->status === 'waiting') $this->accept($call, $userId);
            if ($call->status === 'completed') return $call;

            foreach ($items as $row) {
                $this->stock->use($row['item_id'], $row['quantity'], $call->id);
            }
            $call->update(['status' => 'completed', 'completed_at' => now()]);
            return $call;
        });
    }
}