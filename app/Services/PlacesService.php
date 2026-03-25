<?php

namespace App\Services;

use App\Models\Reservation;

class PlacesService
{
    public function getReservations(?int $parkingId = null)
    {
        return Reservation::with(['place.parking', 'voiture.user'])
            ->when($parkingId, fn($query) =>
            $query->whereHas('place', fn($q) => $q->where('parking_id', $parkingId))
            )
            ->get()
            ->sortBy(fn($res) => $res->place->num_place)
            ->values();
    }

    public function deleteReservation(int $id): void
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->place->update(['disponible' => true]);
        $reservation->delete();
    }
}
