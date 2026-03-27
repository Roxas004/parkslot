<?php

namespace App\Services;
use App\Models\Voiture;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

Class AjouterVoitureService
{
    public function AjouterVoitureService(string $modèleVoiture, string $immatriculation, User $user): void
    {
        Voiture::create([
            'modele_voiture' => $modèleVoiture,
            'immatriculation' => $immatriculation,
            'user_id' => $user->id,

        ]);
    }

    public function getVoitureUser()
    {
        return  Auth::user()
            ->voitures()
            ->get();
    }
}
