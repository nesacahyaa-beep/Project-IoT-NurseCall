<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Room;
use App\Models\Call;
use PhpMqtt\Client\MqttClient;

class DashboardNurseCall extends Component
{
    public function finish(int $callId)
    {
        $call = Call::with('room')->find($callId);

        if ($call) {
            // 1. Update status panggilan di database
            $call->update([
                'status'           => 'completed',
                'completed_at'     => now(),
                'response_seconds' => $call->called_at ? now()->diffInSeconds($call->called_at) : 0,
            ]);

            // 2. Kirim sinyal RESET ke ESP32 Wokwi
            $roomCode = strtolower($call->room?->code ?? 'R101');
            $topicCode = ($roomCode === 'r101') ? 'kamar101' : $roomCode;
            $cmdTopic = "nursecall/{$topicCode}/cmd";

            try {
                $server   = env('MQTT_HOST', 'broker.hivemq.com');
                $port     = (int) env('MQTT_PORT', 1883);
                $clientId = 'laravel-publisher-' . uniqid();

                $mqtt = new MqttClient($server, $port, $clientId);
                $mqtt->connect();
                $mqtt->publish($cmdTopic, json_encode(['cmd' => 'RESET']), 0);
                $mqtt->disconnect();
            } catch (\Throwable $e) {
                // Abaikan jika broker MQTT terputus sementara
            }
        }
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $rooms = Room::all();
        $activeCalls = Call::with('room')->active()->get();

        return view('livewire.dashboard-nurse-call', [
            'rooms'       => $rooms,
            'activeCalls' => $activeCalls,
            'normal'      => $rooms->filter(fn($r) => $r->display_status === 'normal')->count(),
            'offline'     => $rooms->filter(fn($r) => $r->is_offline)->count(),
        ]);
    }
}