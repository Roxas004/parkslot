<?php

namespace App\Services;

use App\Models\FileAttente;
use Illuminate\Support\Facades\Auth;

class VosReservationService
{
    /**
     * Retourne les réservations actives de l'utilisateur connecté.
     */
    public function reservationEnCours()
    {
        return Auth::user()
            ->reservations()
            ->with(['place.parking', 'voiture'])
            ->where(function ($query) {
                $query->whereNull('fin_reservation')
                    ->orWhere('fin_reservation', '>', now());
            })
            ->orderByDesc('debut_reservation')
            ->get();
    }

    public function historiqueResa()
    {
        return Auth::user()
            ->reservations()
            ->with(['place.parking', 'voiture'])
            ->whereNotNull('fin_reservation')
            ->where('fin_reservation', '<=', now())
            ->orderByDesc('fin_reservation')
            ->get();
    }
    public function fileAttenteEnCours()
    {
        return FileAttente::with(['voiture', 'parking'])
            ->whereHas('voiture', fn ($q) => $q->where('user_id', Auth::id()))
            ->orderBy('position')
            ->get();
    }
}
