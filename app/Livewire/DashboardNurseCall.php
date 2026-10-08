<?php

namespace App\Livewire;

use Livewire\Component;

class DashboardNurseCall extends Component
{
    public $rooms = [];
    public $selectedRoomForModal = null;
    public $itemUsed = '';
    public $quantityUsed = 1;

    public function mount()
    {
        // Data simulasi kamar & panggilan darurat
        $this->rooms = [
            ['id' => 101, 'name' => 'Kamar 101 (VVIP)', 'status' => 'NORMAL', 'patient' => 'Bpk. Budi', 'temp' => 24.5, 'humidity' => 55, 'call_active' => false, 'call_time' => null, 'priority' => 'NORMAL'],
            ['id' => 102, 'name' => 'Kamar 102 (VIP)', 'status' => 'EMERGENCY', 'patient' => 'Ibu Siti', 'temp' => 26.1, 'humidity' => 60, 'call_active' => true, 'call_time' => '17:05', 'priority' => 'EMERGENCY'],
            ['id' => 103, 'name' => 'Kamar 103 (Kelas 1)', 'status' => 'WARNING', 'patient' => 'An. Ahmad', 'temp' => 28.0, 'humidity' => 65, 'call_active' => true, 'call_time' => '17:08', 'priority' => 'URGENT'],
            ['id' => 104, 'name' => 'Kamar 104 (Kelas 1)', 'status' => 'NORMAL', 'patient' => 'Bpk. Joko', 'temp' => 23.8, 'humidity' => 50, 'call_active' => false, 'call_time' => null, 'priority' => 'NORMAL'],
        ];
    }

    public function openUsageModal($roomId)
    {
        $this->selectedRoomForModal = $roomId;
    }

    public function finishCallWithUsage()
    {
        foreach ($this->rooms as &$room) {
            if ($room['id'] === $this->selectedRoomForModal) {
                $room['call_active'] = false;
                $room['status'] = 'NORMAL';
                $room['priority'] = 'NORMAL';
            }
        }
        $this->selectedRoomForModal = null;
        $this->itemUsed = '';
        $this->quantityUsed = 1;
    }

    public function render()
    {
        return view('components.dashboard-nurse-call');
    }
}