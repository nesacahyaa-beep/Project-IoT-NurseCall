<?php

namespace App\Services;

use App\Models\Call;

class CallService
{
    public function accept(Call $call, int|string|null $userId): void
    {
        // isi logika accept di sini, misalnya:
        // $call->update(['status' => 'accepted', 'accepted_by' => $userId]);
    }

    public function complete(Call $call, array $usage, int|string|null $userId): void
    {
        // isi logika complete di sini, misalnya:
        // $call->update(['status' => 'completed', 'completed_by' => $userId]);

        // proses pemakaian barang untuk mengurangi stok:
        // foreach ($usage as $row) {
        //     $itemId = $row['item_id'];
        //     $qty    = (int) $row['quantity'];
        //     // kurangi stok item $itemId sebanyak $qty
        // }
    }
}