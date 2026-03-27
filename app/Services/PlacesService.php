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

    public function creerPlaces(int $parkingId): void
    {
        $parking = Parking::findOrFail($parkingId);

        $lastNumero = Place::where('parking_id', $parking->id)
            ->max('num_place');

        $nouveauNumero = $lastNumero ? $lastNumero + 1 : 1;

        Place::create([
            'num_place' => $nouveauNumero,
            'parking_id' => $parking->id,
            'disponible' => true
        ]);
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
