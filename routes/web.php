<?php

use App\Http\Controllers\WeatherDashboardController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Weather Dashboard Routes (Requires Authentication)
Route::prefix('weather')->name('weather.')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [WeatherDashboardController::class, 'index'])->name('dashboard');
    Route::get('/data', [WeatherDashboardController::class, 'getData'])->name('data');
    Route::get('/statistics', [WeatherDashboardController::class, 'getStatistics'])->name('statistics');
    Route::get('/stations', [WeatherDashboardController::class, 'getStations'])->name('stations');
    Route::get('/test-connection', [WeatherDashboardController::class, 'testConnection'])->name('test');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
