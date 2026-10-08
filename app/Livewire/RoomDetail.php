<?php

namespace App\Livewire;

use App\Models\Room;
use Livewire\Attributes\{Layout, Title};
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Detail Kamar')]
class RoomDetail extends Component
{
    public Room $room;

    public function mount(Room $room): void
    {
        $this->room = $room;
    }

    public function render()
    {
        $room = Room::with('activeCall')->findOrFail($this->room->id);

        return view('livewire.room-detail', [
            'room'  => $room,
            'calls' => $room->calls()->latest('called_at')->limit(10)->get(),
        ]);
    }
}