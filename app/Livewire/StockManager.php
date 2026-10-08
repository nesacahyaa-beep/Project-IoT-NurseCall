<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Item;
use App\Models\Call;
use App\Models\ItemUsage;
use App\Services\StockService;
use Exception;

class StockManager extends Component
{
    public $name = '';
    public $unit = '';
    public $stock = '';
    public $minStock = '';

    public $useItem = '';
    public $useQty = '';
    public $useCall = '';
    public $useNote = '';

    public $inItem = '';
    public $inQty = '';
    public $inNote = '';

    public function addItem(StockService $stockService)
    {
        $this->validate([
            'name'     => 'required|string|max:255',
            'unit'     => 'required|string|max:50',
            'stock'    => 'required|numeric|min:0',
            'minStock' => 'required|numeric|min:0',
        ], [
            'name.required'     => 'Nama barang wajib diisi.',
            'unit.required'     => 'Satuan wajib diisi.',
            'stock.required'    => 'Stok awal wajib diisi.',
            'minStock.required' => 'Minimum stok wajib diisi.',
        ]);

        try {
            $stockService->create([
                'name'      => $this->name,
                'unit'      => $this->unit,
                'stock'     => $this->stock,
                'min_stock' => $this->minStock,
            ]);

            $this->reset(['name', 'unit', 'stock', 'minStock']);
            session()->flash('ok', 'Jenis barang berhasil ditambahkan!');
        } catch (Exception $e) {
            $this->addError('name', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    public function recordUsage(StockService $stockService)
    {
        $this->validate([
            'useItem' => 'required|exists:items,id',
            'useQty'  => 'required|numeric|min:1',
        ], [
            'useItem.required' => 'Pilih barang yang digunakan.',
            'useQty.required'  => 'Jumlah penggunaan wajib diisi.',
        ]);

        try {
            $stockService->use(
                (int) $this->useItem,
                (int) $this->useQty,
                $this->useCall ? (int) $this->useCall : null,
                $this->useNote
            );

            $this->reset(['useItem', 'useQty', 'useCall', 'useNote']);
            session()->flash('ok', 'Pemakaian barang berhasil dicatat!');
        } catch (Exception $e) {
            $this->addError('useItem', $e->getMessage());
        }
    }

    public function receiveStock(StockService $stockService)
    {
        $this->validate([
            'inItem' => 'required|exists:items,id',
            'inQty'  => 'required|numeric|min:1',
        ], [
            'inItem.required' => 'Pilih barang yang diterima.',
            'inQty.required'  => 'Jumlah penerimaan wajib diisi.',
        ]);

        try {
            $stockService->add(
                (int) $this->inItem,
                (int) $this->inQty,
                $this->inNote
            );

            $this->reset(['inItem', 'inQty', 'inNote']);
            session()->flash('ok', 'Stok barang berhasil ditambah!');
        } catch (Exception $e) {
            $this->addError('inItem', 'Gagal menambah stok: ' . $e->getMessage());
        }
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('livewire.stock-manager', [
            'items'  => Item::all(),
            'calls'  => Call::with('room')->latest()->take(10)->get(),
            'usages' => ItemUsage::with(['item', 'call.room'])->latest()->take(15)->get(),
        ]);
    }
}