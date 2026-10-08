<?php

namespace App\Livewire;

use App\Models\Call;
use App\Services\CallService;
use Livewire\Component;

class CallQueue extends Component
{
    public function accept(int $id, CallService $svc)
    {
        $svc->accept(Call::findOrFail($id), auth()->id());
    }

    public function complete(int $id, CallService $svc)
    {
        $svc->complete(Call::findOrFail($id), [], auth()->id());
    }

    public function render()
    {
        $calls = Call::with('room')->active()
            ->orderByRaw("CASE WHEN level = 'emergency' THEN 0 ELSE 1 END")
            ->orderBy('called_at')
            ->get();

        return view('livewire.call-queue', compact('calls'));
    }
}