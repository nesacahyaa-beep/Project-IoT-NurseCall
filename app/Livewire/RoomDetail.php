<?php

namespace App\Livewire;

use App\Models\Room;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class RoomDetail extends Component
{
    public mixed $room = null;
    public bool $isComfortable = true;

    public array $timeLabels = ['08:00', '09:00', '10:00', '11:00', '12:00', '13:00'];
    public array $tempData = [];
    public array $humidityData = [];

    public function mount(mixed $room): void
    {
        $roomId = is_object($room) ? ($room->id ?? 1) : $room;
        $foundRoom = Room::find($roomId);

        if ($foundRoom) {
            $this->room = $foundRoom;
        } else {
            // Pemetaan data dummy disamakan dengan tampilan dashboard
            $mockRooms = [
                1 => ['name' => 'Kamar 101', 'type' => 'VVIP', 'temp' => 24.5, 'hum' => 55, 'status' => 'Normal'],
                2 => ['name' => 'Kamar 102', 'type' => 'VIP', 'temp' => 26.1, 'hum' => 60, 'status' => 'Panggilan'],
                3 => ['name' => 'Kamar 103', 'type' => 'Kelas 1', 'temp' => 28.0, 'hum' => 65, 'status' => 'Panggilan'],
                4 => ['name' => 'Kamar 104', 'type' => 'Kelas 1', 'temp' => 23.8, 'hum' => 50, 'status' => 'Normal'],
                5 => ['name' => 'Kamar 105', 'type' => 'Kelas 2', 'temp' => 0.0, 'hum' => 0, 'status' => 'Offline'],
            ];

            $info = $mockRooms[$roomId] ?? [
                'name' => 'Kamar ' . $roomId,
                'type' => 'Rawat Inap',
                'temp' => 24.0,
                'hum' => 50,
                'status' => 'Normal'
            ];

            $this->room = (object)[
                'id' => $roomId,
                'name' => $info['name'],
                'type' => $info['type'],
                'temperature' => $info['temp'],
                'humidity' => $info['hum'],
                'status' => $info['status']
            ];
        }

        // Olah titik grafik sesuai angka suhu kamar
        $baseTemp = (float)($this->room->temperature ?? 24);
        $baseHum = (float)($this->room->humidity ?? 50);

        if ($baseTemp == 0) {
            $this->tempData = [0, 0, 0, 0, 0, 0];
            $this->humidityData = [0, 0, 0, 0, 0, 0];
        } else {
            $this->tempData = [
                round($baseTemp - 0.8, 1),
                round($baseTemp - 0.3, 1),
                round($baseTemp, 1),
                round($baseTemp + 0.4, 1),
                round($baseTemp + 0.2, 1),
                round($baseTemp, 1)
            ];

            $this->humidityData = [
                round($baseHum - 3),
                round($baseHum - 1),
                round($baseHum),
                round($baseHum + 2),
                round($baseHum + 1),
                round($baseHum)
            ];
        }

        // Pengecekan kenyamanan ruangan (Suhu 20-26°C & Kelembapan 40-60%)
        $temp = (float) ($this->room->temperature ?? 24);
        $hum = (float) ($this->room->humidity ?? 50);

        $this->isComfortable = ($temp >= 20.0 && $temp <= 26.0) && ($hum >= 40.0 && $hum <= 60.0);
    }

    public function render()
    {
        return view('livewire.room-detail');
    }
}