<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\DashboardNurseCall;
use App\Livewire\{RoomMap, CallQueue};

// routes/api.php
use App\Http\Controllers\Api\{DeviceController, CallController, ItemController};
use App\Http\Middleware\DeviceKey;
use App\Models\Room;

Route::get('/', RoomMap::class);
Route::get('/antrean', CallQueue::class);

Route::get('/', DashboardNurseCall::class);

// Dari ESP32
Route::middleware(DeviceKey::class)->prefix('device')->group(function () {
    Route::post('sensor',    [DeviceController::class, 'sensor']);
    Route::post('call',      [DeviceController::class, 'call']);
    Route::post('heartbeat', [DeviceController::class, 'heartbeat']);
});

// Untuk dashboard
Route::get('rooms', fn () => Room::with('activeCall')->get()->append(['display_status', 'is_comfortable']));
Route::get('rooms/{room}/readings', fn (Room $room) => $room->readings()
    ->where('recorded_at', '>=', now()->subHours(request('hours', 6)))
    ->orderBy('recorded_at')->get());

Route::get('calls', [CallController::class, 'index']);
Route::get('calls/stats', [CallController::class, 'stats']);
Route::post('calls/{call}/accept',   [CallController::class, 'accept']);
Route::post('calls/{call}/complete', [CallController::class, 'complete']);

Route::apiResource('items', ItemController::class)->except('show');
Route::post('items/{item}/use',     [ItemController::class, 'use']);
Route::post('items/{item}/restock', [ItemController::class, 'restock']);