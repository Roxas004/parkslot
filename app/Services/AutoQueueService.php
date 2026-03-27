<?php

namespace App\Services;

use App\Models\FileAttente;
use App\Models\Place;
class AutoQueueService
{
    protected $reservationService;

    public function __construct(ReservationService $reservationService)
    {
        $this->reservationService = $reservationService;
    }

    public function attribuerPlaceDepuisFile(Place $place)
    {
        $file = FileAttente::where('parking_id', $place->parking_id)
            ->orderBy('position')
            ->first();

        if (!$file) return;

        $this->reservationService->enregistrerReservation(
            user: $file->voiture->user,
            parking: $place->parking,
            immatriculation: $file->voiture->immatriculation
        );

        $file->delete();

        FileAttente::where('parking_id', $place->parking_id)
            ->where('position', '>', 1)
            ->decrement('position', 1);
    }
}
