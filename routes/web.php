<?php

use App\Livewire\DashboardNurseCall;
use App\Livewire\CallQueue;
use App\Livewire\RoomDetail;
use App\Livewire\CallHistory;
use App\Livewire\StockManager;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| NurseCall
|--------------------------------------------------------------------------
| Halaman utama menggunakan DashboardNurseCall.
*/


// Halaman utama
Route::get('/', DashboardNurseCall::class)->name('dashboard');


// Peta Kamar / Dashboard
Route::get('/rooms', DashboardNurseCall::class)->name('rooms');


// Antrean
Route::get('/antrean', CallQueue::class)->name('queue');


// Detail kamar
Route::get('/kamar/{room}', RoomDetail::class)->name('room');


// Riwayat panggilan
Route::get('/riwayat', CallHistory::class)->name('history');


// Stok
Route::get('/stok', StockManager::class)->name('stock');