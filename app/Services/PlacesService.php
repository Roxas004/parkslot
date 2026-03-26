<?php

namespace App\Services;

use App\Models\Parking;
use App\Models\Place;
use App\Models\Reservation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PlacesService
{
    public function __construct(
        private AutoQueueService $autoQueueService
    ) {}

    public function getReservations(?int $parkingId = null): Collection
    {
        return Reservation::with(['place.parking', 'voiture.user'])
            ->where(function ($query) {
                $query->whereNull('fin_reservation')
                    ->orWhere('fin_reservation', '>', now());
            })
            ->when(
                $parkingId,
                fn ($q) => $q->whereIn(
                    'place_id',
                    Place::where('parking_id', $parkingId)->pluck('id')
                )
            )
            ->get()
            ->sortBy(fn ($res) => $res->place->num_place)
            ->values();
    }

    public function deleteReservation(int $id): void
    {
        DB::transaction(function () use ($id) {
            $reservation = Reservation::with('place')->findOrFail($id);
            $place = $reservation->place;

            $reservation->traitee = true;
            $reservation->save();
            $reservation->delete();

            // Libère la place
            $place->disponible = true;
            $place->save();

            $this->autoQueueService->attribuerPlaceDepuisFile($place);
        });
    }

    public function getPlaces(?int $parkingId = null): Collection
    {
        return Place::with('parking')
            ->when($parkingId, fn ($q) => $q->where('parking_id', $parkingId))
            ->orderBy('parking_id')
            ->orderBy('num_place')
            ->get();
    }

    public function creerPlaces(int $parkingId, string $numPlace): void
    {
        $parking = Parking::findOrFail($parkingId);

        if (str_contains($numPlace, '-')) {
            [$debut, $fin] = explode('-', $numPlace, 2);
            $debut = (int) trim($debut);
            $fin   = (int) trim($fin);

            if ($debut < 1 || $fin < $debut || ($fin - $debut) > 200) {
                throw new \InvalidArgumentException(
                    'Plage invalide. Utilisez un format "1-20" (max 200 places par opération).'
                );
            }

            for ($i = $debut; $i <= $fin; $i++) {
                Place::firstOrCreate(
                    ['num_place' => (string) $i, 'parking_id' => $parking->id],
                    ['disponible' => true]
                );
            }
        } else {
            Place::firstOrCreate(
                ['num_place' => trim($numPlace), 'parking_id' => $parking->id],
                ['disponible' => true]
            );
        }
    }

    public function modifierPlace(int $placeId, string $nouveauNumero): Place
    {
        $place = Place::findOrFail($placeId);

        if (! $place->disponible) {
            throw new \RuntimeException(
                'Impossible de modifier une place actuellement occupée.'
            );
        }

        $place->update(['num_place' => trim($nouveauNumero)]);

        return $place->fresh();
    }

    public function supprimerPlace(int $placeId): void
    {
        $place = Place::findOrFail($placeId);

        if (! $place->disponible) {
            throw new \RuntimeException(
                "Impossible de supprimer une place occupée. Libérez-la d'abord."
            );
        }

        $place->delete();
    }
}
