<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\VosReservationController;
use App\Http\Controllers\GererUtilisateurController;
use App\Http\Controllers\HistoriqueController;
use App\Http\Controllers\QueueController;
use App\Http\Controllers\PlaceController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'accepted'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/reservation', [ReservationController::class, 'store'])->name('reservation.store');
    Route::get('/reservation', [ReservationController::class, 'index'])->name('reservation.index');
    Route::get('/', [ReservationController::class, 'index']);

    Route::get('/vosreservations', [VosReservationController::class, 'index'])->name('vosreservations');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/utilisateurs', [GererUtilisateurController::class, 'index'])
        ->name('utilisateurs.index');

    Route::post('/utilisateurs/{user}/accepter', [GererUtilisateurController::class, 'accepter'])
        ->name('utilisateurs.accepter');

    Route::post('/utilisateurs/{user}/refuser', [GererUtilisateurController::class, 'refuser'])
        ->name('utilisateurs.refuser');

    Route::delete('/utilisateurs/{user}', [GererUtilisateurController::class, 'supprimer'])
        ->name('utilisateurs.supprimer');

    Route::post('/utilisateurs/{user}/reset-mdp', [GererUtilisateurController::class, 'envoyerResMdp'])
        ->name('utilisateurs.reset-mdp');

    Route::get('/places', [PlaceController::class, 'index'])->name('places');

    Route::get('/fileattente', [QueueController::class, 'index'])->name('fileattente');
    Route::post('/admin/queue/swap', [QueueController::class, 'swap'])->name('admin.queue.swap');

    Route::get('/historique', [HistoriqueController::class, 'index'])->name('historique');

    Route::get('/places-occupees', [PlaceController::class, 'index'])
        ->name('places.index');

    Route::delete('/places-occupees/{id}', [PlaceController::class, 'destroy'])
        ->name('places.destroy');
});



require __DIR__ . '/auth.php';
