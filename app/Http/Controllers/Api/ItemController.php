<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Services\StockService;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index() { return Item::orderBy('name')->get()->append('needs_reorder'); }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100', 'unit' => 'required|string|max:20',
            'stock' => 'required|integer|min:0', 'min_stock' => 'required|integer|min:0',
        ]);
        return Item::create($data);
    }

    public function update(Request $request, Item $item)
    {
        $item->update($request->validate([
            'name' => 'sometimes|string|max:100', 'unit' => 'sometimes|string|max:20',
            'min_stock' => 'sometimes|integer|min:0',
        ]));
        return $item;
    }

    public function destroy(Item $item) { $item->delete(); return response()->noContent(); }

    public function use(Request $request, Item $item, StockService $stock)
    {
        $d = $request->validate(['quantity' => 'required|integer|min:1', 'call_id' => 'nullable|exists:calls,id']);
        return $stock->use($item->id, $d['quantity'], $d['call_id'] ?? null)->append('needs_reorder');
    }

    public function restock(Request $request, Item $item, StockService $stock)
    {
        $d = $request->validate(['quantity' => 'required|integer|min:1', 'note' => 'nullable|string']);
        return $stock->add($item->id, $d['quantity'], $d['note'] ?? null)->append('needs_reorder');
    }
}
