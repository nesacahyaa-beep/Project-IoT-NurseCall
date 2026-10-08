<?php

namespace App\Livewire;

use App\Models\{Call, Item, ItemUsage};
use App\Services\StockService;
use Livewire\Attributes\{Layout, Title};
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Manajemen Stok')]
class StockManager extends Component
{
    // tambah barang
    public string $name = '';
    public string $unit = 'pcs';
    public $stock = 0;
    public $minStock = 0;

    // catat pemakaian
    public $useItem = '';
    public $useQty = 1;
    public $useCall = '';
    public string $useNote = '';

    // terima barang
    public $inItem = '';
    public $inQty = 1;
    public string $inNote = '';

    public function addItem(): void
    {
        $d = $this->validate([
            'name'     => 'required|string|max:100',
            'unit'     => 'required|string|max:20',
            'stock'    => 'required|integer|min:0',
            'minStock' => 'required|integer|min:0',
        ]);

        Item::create([
            'name' => $d['name'], 'unit' => $d['unit'],
            'stock' => $d['stock'], 'min_stock' => $d['minStock'],
        ]);

        $this->reset('name', 'unit', 'stock', 'minStock');
        session()->flash('ok', 'Barang ditambahkan.');
    }

    public function recordUsage(StockManager $svc): void
    {
        $d = $this->validate([
            'useItem' => 'required|exists:items,id',
            'useQty'  => 'required|integer|min:1',
            'useCall' => 'nullable|exists:calls,id',
            'useNote' => 'nullable|string|max:255',
        ]);

        $item = $svc->use((int) $d['useItem'], (int) $d['useQty'],
            $d['useCall'] ? (int) $d['useCall'] : null, $d['useNote'] ?: null);

        session()->flash('ok', 'Pemakaian dicatat.' . ($item->needs_reorder ? " ⚠ {$item->name} perlu reorder!" : ''));
        $this->reset('useItem', 'useQty', 'useCall', 'useNote');
    }

    public function receiveStock(StockService $svc): void
    {
        $d = $this->validate([
            'inItem' => 'required|exists:items,id',
            'inQty'  => 'required|integer|min:1',
            'inNote' => 'nullable|string|max:255',
        ]);

        $svc->add((int) $d['inItem'], (int) $d['inQty'], $d['inNote'] ?: null);

        session()->flash('ok', 'Stok ditambahkan.');
        $this->reset('inItem', 'inQty', 'inNote');
    }

    public function render()
    {
        return view('livewire.stock-manager', [
            'items'  => Item::orderBy('name')->get(),
            'calls'  => Call::with('room')->latest('called_at')->limit(20)->get(),
            'usages' => ItemUsage::with('item', 'call.room')->latest()->limit(15)->get(),
        ]);
    }
}