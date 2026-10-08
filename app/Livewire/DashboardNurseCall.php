<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Schema;
use App\Models\Room;
use App\Models\Call;

class DashboardNurseCall extends Component
{
    public function finish(int $callId)
    {
        try {
            if (class_exists(Call::class) && Schema::hasTable('calls')) {
                $call = Call::find($callId);
                if ($call) {
                    $call->update(['status' => 'finished']);
                    if ($call->room) {
                        $call->room->update(['state' => 'normal']);
                    }
                }
            }
        } catch (\Throwable $e) {
            // Abaikan error DB
        }
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $rooms = collect([]);
        $activeCalls = collect([]);
        $normal = 0;
        $offline = 0;

        try {
            if (class_exists(Room::class) && Schema::hasTable('rooms')) {
                $rooms = Room::all();
                $normal = Room::where('state', 'normal')->orWhere('status', 'normal')->count();
                $offline = Room::where('state', 'offline')->orWhere('status', 'offline')->count();
            }
        } catch (\Throwable $e) {
            $rooms = collect([]);
        }

        try {
            if (class_exists(Call::class) && Schema::hasTable('calls')) {
                $query = Call::query();
                if (method_exists(Call::class, 'room')) {
                    $query->with('room');
                }
                $activeCalls = $query->whereIn('status', ['active', 'calling', 'emergency', 'panggilan', 'CALLING', 'EMERGENCY'])->get();
            }
        } catch (\Throwable $e) {
            $activeCalls = collect([]);
        }

        return view('livewire.dashboard-nurse-call', [
            'rooms' => $rooms,
            'activeCalls' => $activeCalls,
            'normal' => $normal,
            'offline' => $offline,
        ]);
    }
}