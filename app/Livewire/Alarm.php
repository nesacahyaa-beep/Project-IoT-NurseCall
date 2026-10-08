<?php

namespace App\Livewire;

use App\Models\Call;
use Livewire\Component;

class Alarm extends Component
{
    public function check(): void
    {
        $waiting = Call::where('status', 'waiting');

        $this->dispatch('alarm-state',
            waiting: (clone $waiting)->count(),
            emergency: (clone $waiting)->where('level', 'emergency')->count(),
        );
    }

    public function render()
    {
        return view('livewire.alarm');
    }
}