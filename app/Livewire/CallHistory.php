<?php

namespace App\Livewire;

use App\Models\{Call, Room};
use Livewire\Attributes\{Layout, Title};
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Riwayat Panggilan')]
class CallHistory extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $from = '';
    public string $to = '';
    public string $roomId = '';
    public string $level = '';

    public function updated($property): void
    {
        $this->resetPage();
    }

    protected function query()
    {
        return Call::query()
            ->when($this->from, fn ($q) => $q->whereDate('called_at', '>=', $this->from))
            ->when($this->to, fn ($q) => $q->whereDate('called_at', '<=', $this->to))
            ->when($this->roomId, fn ($q) => $q->where('room_id', $this->roomId))
            ->when($this->level, fn ($q) => $q->where('level', $this->level));
    }

    public function render()
    {
        $base = $this->query();

        return view('livewire.call-history', [
            'calls' => $this->query()->with('room')->latest('called_at')->paginate(15),
            'rooms' => Room::orderBy('name')->get(),
            'stats' => [
                'total' => (clone $base)->count(),
                'avg'   => round((float) (clone $base)->whereNotNull('response_seconds')->avg('response_seconds'), 1),
                'max'   => (int) (clone $base)->max('response_seconds'),
            ],
        ]);
    }
}