<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\{Room, SensorReading};
use App\Services\CallService;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function sensor(Request $request)
    {
        $data = $request->validate([
            'room'        => 'required|string|exists:rooms,code',
            'temperature' => 'required|numeric|between:-10,60',
            'humidity'    => 'required|numeric|between:0,100',
        ]);

        $room = Room::where('code', $data['room'])->firstOrFail();
        $room->update([
            'temperature' => $data['temperature'],
            'humidity'    => $data['humidity'],
            'last_seen_at' => now(),
        ]);

        // Batasi penyimpanan histori agar tabel tidak membengkak
        $last = $room->readings()->latest('recorded_at')->first();
        if (! $last || $last->recorded_at->lt(now()->subSeconds(config('nursecall.sensor_store_every')))) {
            SensorReading::create([
                'room_id' => $room->id,
                'temperature' => $data['temperature'],
                'humidity' => $data['humidity'],
                'recorded_at' => now(),
            ]);
        }

        return response()->json(['ok' => true, 'command' => $room->deviceCommand()]);
    }

    public function call(Request $request, CallService $calls)
    {
        $data = $request->validate([
            'room'  => 'required|string|exists:rooms,code',
            'level' => 'required|in:normal,emergency',
        ]);

        $room = Room::where('code', $data['room'])->firstOrFail();
        $room->update(['last_seen_at' => now()]);
        $call = $calls->receive($room, $data['level']);

        return response()->json(['ok' => true, 'call_id' => $call->id, 'command' => $room->deviceCommand()]);
    }

    public function heartbeat(Request $request)
    {
        $data = $request->validate(['room' => 'required|string|exists:rooms,code']);
        $room = Room::where('code', $data['room'])->firstOrFail();
        $room->update(['last_seen_at' => now()]);

        return response()->json(['ok' => true, 'command' => $room->deviceCommand()]);
    }
}
