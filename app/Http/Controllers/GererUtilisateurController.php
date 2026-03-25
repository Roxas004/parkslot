<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\GererUtilisateurService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GererUtilisateurController extends Controller
{
    public function __construct(
        private readonly GererUtilisateurService $gererUtilisateurService
    ) {}

    public function index(Request $request): View
    {
        $users = $this->gererUtilisateurService->ListerUtilisateurs(
            $request->get('search'),
            $request->get('filter'),
        );

        return view('admin.gererUtilisateur', compact('users'));
    }

    public function accepter(User $user): RedirectResponse
    {
        $this->gererUtilisateurService->AccepterUtilisateur($user);

        return back()->with('success', 'Utilisateur accepté.');
    }

    public function refuser(User $user): RedirectResponse
    {
        $this->gererUtilisateurService->RefuserUtilisateur($user);

        return back()->with('success', 'Utilisateur refusé.');
    }

    public function supprimer(User $user): RedirectResponse
    {
        $this->gererUtilisateurService->SupprimerUtilisateur($user);

        return back()->with('success', 'Utilisateur supprimé.');
    }

    public function envoyerResMdp(User $user): RedirectResponse
    {
        $this->gererUtilisateurService->EnvoyerResMdp($user);

        return back()->with('success', 'Email de réinitialisation envoyé.');
    }
}
