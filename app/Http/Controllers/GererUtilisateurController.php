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
        private GererUtilisateurService $gererUtilisateurService
    ) {}

    public function index(Request $request): View
    {
        $users = $this->gererUtilisateurService->listerUtilisateurs(
            $request->string('search')->toString() ?: null,
            $request->string('filter')->toString() ?: null,
        );

        return view('admin.gererUtilisateur', compact('users'));
    }

    public function accepter(User $user): RedirectResponse
    {
        $this->gererUtilisateurService->accepterUtilisateur($user);

        return back();
    }

    public function refuser(User $user): RedirectResponse
    {
        $this->gererUtilisateurService->refuserUtilisateur($user);

        return back();
    }

    public function supprimer(User $user): RedirectResponse
    {
        $this->gererUtilisateurService->supprimerUtilisateur($user);

        return back();
    }

    public function envoyerResMdp(User $user): RedirectResponse
    {
        $this->gererUtilisateurService->envoyerResetMdp($user);

        return back();
    }
}
