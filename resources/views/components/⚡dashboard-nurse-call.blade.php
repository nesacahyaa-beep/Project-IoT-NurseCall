<?php

namespace App\Livewire;

use Livewire\Component;

class DashboardNurseCall extends Component
{
    public $selectedRoomForModal = null;
    public $usedItem = '';
    public $usedQty = 1;

    // Data dummy simulasi kamar real-time
    public $roomsData = [
        ['id' => 101, 'number' => 'Kamar 101 (VVIP)', 'status' => 'normal', 'temp' => 24.5, 'humidity' => 55, 'calling_since' => 0],
        ['id' => 102, 'number' => 'Kamar 102 (VIP)', 'status' => 'emergency', 'temp' => 26.1, 'humidity' => 60, 'calling_since' => 140],
        ['id' => 103, 'number' => 'Kamar 103 (Kelas 1)', 'status' => 'calling', 'temp' => 28.0, 'humidity' => 65, 'calling_since' => 45],
        ['id' => 104, 'number' => 'Kamar 104 (Kelas 1)', 'status' => 'normal', 'temp' => 23.8, 'humidity' => 50, 'calling_since' => 0],
        ['id' => 105, 'number' => 'Kamar 105 (Kelas 2)', 'status' => 'offline', 'temp' => 0, 'humidity' => 0, 'calling_since' => 0],
    ];

    public function acceptCall($callId)
    {
        foreach ($this->roomsData as &$room) {
            if ($room['id'] == $callId) {
                // Mengubah status panggilan darurat menjadi terlayani/calling
                $room['status'] = 'calling';
            }
        }
    }

    public function openFinishModal($callId)
    {
        $this->selectedRoomForModal = $callId;
    }

    public function finishCallWithUsage()
    {
        foreach ($this->roomsData as &$room) {
            if ($room['id'] == $this->selectedRoomForModal) {
                $room['status'] = 'normal';
                $room['calling_since'] = 0;
            }
        }

        // Reset form modal SCM
        $this->selectedRoomForModal = null;
        $this->usedItem = '';
        $this->usedQty = 1;
    }

    public function render()
    {
        // Hitung statistik banner secara dinamis
        $stats = [
            'normal' => collect($this->roomsData)->where('status', 'normal')->count(),
            'calling' => collect($this->roomsData)->whereIn('status', ['calling', 'emergency'])->count(),
            'offline' => collect($this->roomsData)->where('status', 'offline')->count(),
        ];

        // Filter antrean panggilan aktif
        $activeCalls = collect($this->roomsData)
            ->whereIn('status', ['calling', 'emergency'])
            ->values()
            ->toArray();

        return view('livewire.dashboard-nurse-call', [
            'stats' => $stats,
            'activeCalls' => $activeCalls,
            'roomsList' => $this->roomsData,
        ]);
    }
}