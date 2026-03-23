<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\VosReservationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GererUtilisateurController;
use App\Http\Controllers\HistoriqueController;
use App\Http\Controllers\QueueController;
use App\Http\Controllers\PlaceController;

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

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/utilisateurs', [GererUtilisateurController::class, 'index'])->name('utilisateurs');
    Route::get('/places', [PlaceController::class, 'index'])->name('places');
    Route::get('/fileattente', [QueueController::class, 'index'])->name('fileattente');
    Route::get('/historique', [HistoriqueController::class, 'index'])->name('historique');
});
require __DIR__.'/auth.php';
