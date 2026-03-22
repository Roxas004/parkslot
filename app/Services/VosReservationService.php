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
            ->with(['getPlace.getParking', 'getVoiture'])
            ->where(function ($query) {
                $query->whereNull('fin_reservation')
                    ->orWhere('fin_reservation', '>', now());
            })
            ->orderByDesc('debut_reservation')
            ->get();
    }

    /**
     * Retourne les réservations passées de l'utilisateur connecté.
     */
    public function historiqueResa()
    {
        return Auth::user()
            ->reservations()
            ->with(['getPlace.getParking', 'getVoiture'])
            ->whereNotNull('fin_reservation')
            ->where('fin_reservation', '<=', now())
            ->orderByDesc('fin_reservation')
            ->get();
    }

    /**
     * Retourne les entrées en file d'attente de l'utilisateur connecté.
     */
    public function fileAttenteEnCours()
    {
        return FileAttente::with(['getVoitureListeAttente', 'getParkingListeAttente'])
            ->whereHas('getVoitureListeAttente', fn ($q) => $q->where('user_id', Auth::id()))
            ->orderBy('position')
            ->get();
    }
}
