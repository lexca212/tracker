<?php

use App\Http\Controllers\LocationController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\LinkUndanganController;
use Illuminate\Support\Facades\Route;

Route::get('/', [TrackingController::class, 'index'])->name('share.panel');
Route::post('/share/create', [TrackingController::class, 'store'])->name('share.create');
Route::get('/share/{token}', [TrackingController::class, 'show'])->name('share.show');
Route::post('/share/{token}/update', [TrackingController::class, 'update'])->name('share.update');
Route::get('/share/{token}/update', [TrackingController::class, 'redirectUpdateGet']);
Route::delete('/share/{token}', [TrackingController::class, 'destroy'])->name('share.destroy');

Route::post('/link-undangan', [LinkUndanganController::class, 'store'])->name('link-undangan.store');
Route::get('/link-undangan/{id}', [LinkUndanganController::class, 'show'])->name('link-undangan.show');

Route::get('/location', [LocationController::class, 'index'])->name('location.index');
Route::post('/lookup', [LocationController::class, 'lookup'])->name('location.lookup');
Route::post('/send-location', [LocationController::class, 'sendLocation'])->name('location.send');
Route::get('/undangan', function () {
    return view('undangan');
})->name('undangan');
