<?php

namespace App\Services;

use App\Models\Parking;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ReservationService
{
    public function verifierDisponibilite(Parking $parking): bool
    {
        return $parking->places()->where('disponible', true)->exists();
    }

    public function enregistrerReservation(User $user, Parking $parking, string $immatriculation): Reservation
    {
        $voiture = $user->voitures()
            ->where('immatriculation', strtoupper($immatriculation))
            ->firstOrFail();

        $place = $parking->places()
            ->where('disponible', true)
            ->inRandomOrder()
            ->firstOrFail();

        $fin = now()->addMinutes(60);

        $reservation = Reservation::create([
            'debut_reservation' => now(),
            'fin_reservation'   => $fin,
            'user_id'           => $user->id,
            'place_id'          => $place->id,
            'voiture_id'        => $voiture->id,
        ]);

        $place->disponible = false;
        $place->save();

        return $reservation;
    }
    public function trouverParking(string $nom): Parking
    {
        return Parking::where('lib_parking', $nom)
            ->firstOrFail();
    }
    public function mettreEnListeAttente(User $user, Parking $parking, string $immatriculation): void
    {
        $voiture = $user->voitures()
            ->where('immatriculation', strtoupper($immatriculation))
            ->firstOrFail();


        $prochainePosition = DB::table('file_attente')
            ->where('parking_id', $parking->id)
            ->max('position') ?? 0;

        DB::table('file_attente')->insert([
            'voiture_id' => $voiture->id,
            'parking_id' => $parking->id,
            'position'   => $prochainePosition + 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
