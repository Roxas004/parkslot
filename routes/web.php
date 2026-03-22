<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\VosReservationController;
use Illuminate\Support\Facades\Route;

Route::post('/reservation', [ReservationController::class, 'store'])->middleware('auth')->name('reservation.store');

Route::get('/vosreservations', [VosReservationController::class, 'index'])
    ->name('vosreservations');


Route::get('/', function () {
    return view('utilisateur.reservations');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
