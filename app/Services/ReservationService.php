<?php

namespace App\Services;

use App\Models\Parking;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Voiture;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\FileAttente;

class ReservationService
{
    public function verifierDisponibilite(Parking $parking): bool
    {
        return $parking->places()->where('disponible', true)->exists();
    }

    public function aDejaUneReservationActive(User $user, Parking $parking): bool
    {
        return Reservation::where('user_id', $user->id)
            ->whereHas('place', fn ($q) => $q->where('parking_id', $parking->id))
            ->where('fin_reservation', '>', now())
            ->exists();
    }

    public function estEnFileAttente(User $user, Parking $parking): bool
    {
        return FileAttente::where('parking_id', $parking->id)
            ->whereHas('voiture', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->exists();
    }

    public function enregistrerReservation(User $user, Parking $parking, string $immatriculation): void
    {
        $voiture = $user->voitures()
            ->where('immatriculation', strtoupper($immatriculation))
            ->firstOrFail();

        $place = $parking->places()
            ->where('disponible', true)
            ->inRandomOrder()
            ->firstOrFail();

        $fin = now()->addMinutes(60);

       Reservation::create([
            'debut_reservation' => now(),
            'fin_reservation'   => $fin,
            'user_id'           => $user->id,
            'place_id'          => $place->id,
            'voiture_id'        => $voiture->id,
        ]);

        $place->disponible = false;
        $place->save();
    }

    public function trouverParking(string $nom): Parking
    {
        return Parking::where('lib_parking', $nom)->firstOrFail();
    }

    public function mettreEnListeAttente(User $user, Parking $parking, string $immatriculation): void
    {
        $voiture = $user->voitures()
            ->where('immatriculation', strtoupper($immatriculation))
            ->firstOrFail();

        $prochainePosition = FileAttente::where('parking_id', $parking->id)
            ->max('position') ?? 0;

        FileAttente::create([
            'voiture_id' => $voiture->id,
            'parking_id' => $parking->id,
            'position'   => $prochainePosition + 1,
        ]);
    }

    public function getParkings()
    {
        return Parking::select('lib_parking')->orderBy('lib_parking')->get();
    }

    public function getVoituresUtilisateur()
    {
        return Voiture::where('user_id', Auth::id())
            ->select('immatriculation')
            ->orderBy('immatriculation')
            ->get();
    }
}
