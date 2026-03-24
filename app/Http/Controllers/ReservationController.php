<?php

namespace App\Http\Controllers;
use App\Services\ReservationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    protected $ReservationService;

    public function __construct(ReservationService $reservationService)
    {
        $this->ReservationService = $reservationService;
    }
    public function index() {
        return view('utilisateur.reservations');
    }

    public function store(Request $request)
    {
        $request->validate([
            'parking'         => 'required|string|max:255',
            'immatriculation' => 'required|string|max:20',
        ]);

        $user            = Auth::user();
        $parking         = $request->input('parking');
        $immatriculation = $request->input('immatriculation');

        $parking= $this->ReservationService->trouverParking($parking);
        $disponible = $this->ReservationService->verifierDisponibilite($parking);

        if ($disponible) {
            $reservation = $this->ReservationService->enregistrerReservation(
                user: $user,
                parking: $parking,
                immatriculation: $immatriculation
            );

            return redirect()->back()->with('success', 'Votre réservation a bien été enregistrée.');
        }

        $this->ReservationService->mettreEnListeAttente(
            user: $user,
            parking: $parking,
            immatriculation: $immatriculation
        );

        return redirect()->back()->with('info', 'Plus de places disponibles. Vous avez été ajouté à la liste d\'attente.');
    }
}
