<?php

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/rooms/{room}/readings', function (Room $room) {
    return $room->readings()
        ->where('recorded_at', '>=', now()->subHours((int) request('hours', 6)))
        ->latest()
        ->get();
});