<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Parking;
use App\Models\Reservation;
use App\Models\User;
use App\Services\AjouterVoitureService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AjouterVoitureController extends Controller

{  private AjouterVoitureService $ajouterVoitureService;

    public function __construct(AjouterVoitureService $ajouterVoitureService)
    {
        $this->ajouterVoitureService = $ajouterVoitureService;
    }

    public function store(Request $request)
    {

        $user    = Auth::user();
        $donnees = $request->validate([
            'immatriculation' => ['required', 'string', 'max:9'],
            'modelvoiture' => ['required', 'string', 'max:20'],
        ]);

        $this->ajouterVoitureService->AjouterVoitureService($donnees['modelvoiture'],$donnees['immatriculation'], $user);
        return redirect()->back();
    }
}
