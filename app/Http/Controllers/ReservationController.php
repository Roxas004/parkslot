<?php

namespace App\Http\Controllers;

use App\Services\ReservationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    protected ReservationService $reservationService;

    public function __construct(ReservationService $reservationService)
    {
        $this->reservationService = $reservationService;
    }

    public function index()
    {
        return view('utilisateur.reservations');
    }

    public function getParkings()
    {
        return response()->json($this->reservationService->getParkings());
    }

    public function getVoituresUtilisateur()
    {
        return response()->json($this->reservationService->getVoituresUtilisateur());
    }

    public function store(Request $request)
    {
        $donnees = $request->validate([
            'parking'         => ['required', 'string', 'max:255'],
            'immatriculation' => ['required', 'string', 'max:20'],
        ]);

        $user    = Auth::user();
        $parking = $this->reservationService->trouverParking($donnees['parking']);

        if ($this->reservationService->aDejaUneReservationActive($user, $parking)) {
            return redirect()->back()->with(
                'warning',
                'Vous avez déjà une réservation active dans ce parking.
                 Vous ne pouvez pas en faire une nouvelle.'
            );
        }

        if ($this->reservationService->estEnFileAttente($user, $parking)) {
            return redirect()->back()->with(
                'warning',
                'Vous êtes déjà en liste d\'attente pour ce parking.'
            );
        }

        if ($this->reservationService->verifierDisponibilite($parking)) {
            $this->reservationService->enregistrerReservation(
                user:           $user,
                parking:        $parking,
                immatriculation: $donnees['immatriculation']
            );

            return redirect()->back()->with('success', 'Votre réservation a bien été enregistrée.');
        }

        $this->reservationService->mettreEnListeAttente(
            user:           $user,
            parking:        $parking,
            immatriculation: $donnees['immatriculation']
        );

        return redirect()->back()->with(
            'info',
            'Plus de places disponibles. Vous avez été ajouté à la liste d\'attente.'
        );
    }
}
