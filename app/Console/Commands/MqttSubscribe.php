<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpMqtt\Client\MqttClient;
use App\Models\Room;
use App\Models\Call;
use App\Models\SensorReading;

class MqttSubscribe extends Command
{
    protected $signature = 'mqtt:subscribe';
    protected $description = 'Mendengarkan data MQTT telemetry dan emergency dari Wokwi';

    public function handle()
    {
        $server   = env('MQTT_HOST', 'broker.hivemq.com');
        $port     = (int) env('MQTT_PORT', 1883);
        $clientId = 'laravel-subscriber-' . uniqid();

        $this->info("Menghubungkan ke MQTT Broker ({$server}:{$port})...");

        try {
            $mqtt = new MqttClient($server, $port, $clientId);
            $mqtt->connect();
            $this->info("Berhasil terhubung ke MQTT Broker!");

            // Subscribe ke semua kamar di bawah topik nursecall
            $mqtt->subscribe('nursecall/+/data', function (string $topic, string $message) {
                $this->info("\n[MQTT DATA IN] [{$topic}]: {$message}");

                $data = json_decode($message, true);
                if (!$data || !isset($data['room_id'])) return;

                // Cari kamar di database berdasarkan kode (misal: "R101")
                $room = Room::where('code', $data['room_id'])->first();
                if (!$room) {
                    $this->error("Kamar dengan kode '{$data['room_id']}' tidak ditemukan di database!");
                    return;
                }

                $temp  = (float) ($data['temperature'] ?? 0);
                $hum   = (float) ($data['humidity'] ?? 0);
                $event = $data['event'] ?? 'PERIODIC_READING';

                // 1. Perbarui status sensor & keaktifan kamar
                $room->update([
                    'temperature'  => $temp,
                    'humidity'     => $hum,
                    'last_seen_at' => now(),
                ]);

                // 2. Simpan riwayat pembacaan sensor
                SensorReading::create([
                    'room_id'     => $room->id,
                    'temperature' => $temp,
                    'humidity'    => $hum,
                    'recorded_at' => now(),
                ]);

                // 3. Catat panggilan darurat jika pemicunya NURSE_CALL atau TEMP_ALERT
                if (in_array($event, ['NURSE_CALL', 'TEMP_ALERT'])) {
                    $level = ($event === 'NURSE_CALL') ? 'emergency' : 'normal';

                    $hasActiveCall = Call::where('room_id', $room->id)->active()->exists();
                    if (!$hasActiveCall) {
                        Call::create([
                            'room_id'   => $room->id,
                            'level'     => $level,
                            'status'    => 'waiting',
                            'called_at' => now(),
                        ]);
                        $this->warn("🚨 [PANGGILAN BARU] Kamar {$room->name} ({$level})");
                    }
                }
            }, 0);

            // Loop utama untuk mendengarkan pesan MQTT terus-menerus
            $mqtt->loop(true);

        } catch (\Exception $e) {
            $this->error("Gagal terhubung ke MQTT Broker: " . $e->getMessage());
        }
    }
}