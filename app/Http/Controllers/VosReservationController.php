<?php

namespace App\Http\Controllers;

use App\Services\VosReservationService;
use Illuminate\View\View;

class VosReservationController extends Controller
{
    public function __construct(
        protected readonly VosReservationService $vosReservationService
    ) {}

    public function index(): View
    {
        return view('utilisateur.vosReservations', [
            'reservationsActives' => $this->vosReservationService->reservationEnCours(),
            'fileAttente'         => $this->vosReservationService->fileAttenteEnCours(),
            'historique'          => $this->vosReservationService->historiqueResa(),
        ]);
    }
}
