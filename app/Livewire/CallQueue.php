<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use App\Models\{Call, Item};
use App\Services\CallService;
use Livewire\Attributes\{Layout, Title};
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Antrean Panggilan')]
class CallQueue extends Component
{
    public ?int $completingId = null;
    public array $usage = [];

   public function accept(int $id, CallService $svc): void
{
    $svc->accept(Call::findOrFail($id), Auth::id());
}

    public function startComplete(int $id): void
    {
        $this->completingId = $id;
        $this->usage = [['item_id' => '', 'quantity' => 1]];
        $this->resetErrorBag();
    }

    public function cancelComplete(): void
    {
        $this->completingId = null;
        $this->usage = [];
        $this->resetErrorBag();
    }

    public function addRow(): void
    {
        $this->usage[] = ['item_id' => '', 'quantity' => 1];
    }

    public function removeRow(int $i): void
    {
        unset($this->usage[$i]);
        $this->usage = array_values($this->usage);
    }

    public function confirmComplete(CallService $svc): void
    {
        $rows = collect($this->usage)
            ->filter(fn ($r) => ! empty($r['item_id']))
            ->map(fn ($r) => ['item_id' => (int) $r['item_id'], 'quantity' => max(1, (int) $r['quantity'])])
            ->values()->all();

        // Jika stok tidak cukup, ValidationException dilempar dan seluruh proses dibatalkan
        $svc->complete(Call::findOrFail($this->completingId), $this->usage, Auth::id());    
        $this->cancelComplete();
    }

    public function render()
    {
        $calls = Call::with('room')->active()
            ->orderByRaw("CASE WHEN level = 'emergency' THEN 0 ELSE 1 END")
            ->orderBy('called_at')
            ->get();

        return view('livewire.call-queue', [
            'calls' => $calls,
            'items' => Item::orderBy('name')->get(),
        ]);
    }
}