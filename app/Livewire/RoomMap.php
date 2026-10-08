<?php

namespace App\Livewire;

use App\Models\Room;
use Livewire\Component;

class RoomMap extends Component
{
    public function render()
    {
        $rooms = Room::with('activeCall')->get()
            ->sortBy(fn ($r) => match ($r->display_status) {
                'emergency' => 0, 'calling' => 1, 'offline' => 2, default => 3,
            })->values();

        return view('livewire.room-map', [
            'rooms'   => $rooms,
            'summary' => [
                'normal'  => $rooms->where('display_status', 'normal')->count(),
                'calling' => $rooms->whereIn('display_status', ['calling', 'emergency'])->count(),
                'offline' => $rooms->where('display_status', 'offline')->count(),
            ],
        ]);
    }
}