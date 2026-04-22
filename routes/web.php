<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TanamController;

/*
|--------------------------------------------------------------------------
| Web Routes - Aplikasi Monitoring Tanaman
|--------------------------------------------------------------------------
*/

// Dashboard Utama
Route::get('/', [TanamController::class, 'dashboard'])->name('dashboard');

// Halaman Monitoring Sensor
Route::get('/monitoring', [TanamController::class, 'monitoring'])->name('monitoring');

// Halaman Riwayat Pompa
Route::get('/pompa', [TanamController::class, 'pompa'])->name('pompa');

// API Routes untuk AJAX (auto-refresh)
Route::prefix('api')->group(function () {
    Route::get('/sensor/latest', [TanamController::class, 'getLatestData'])->name('api.sensor.latest');
    Route::get('/pompa/latest', [TanamController::class, 'getLatestPumpEvents'])->name('api.pompa.latest');
});

Route::get('/api/sensor/history', [TanamController::class, 'getSensorHistory'])->name('api.sensor.history');