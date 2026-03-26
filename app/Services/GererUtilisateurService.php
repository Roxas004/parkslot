<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Password;

class GererUtilisateurService
{
    public function listerUtilisateurs(?string $search, ?string $filter): LengthAwarePaginator
    {
        $query = User::where('role', 'user')
            ->orderBy('approved')
            ->orderBy('name');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('prenom', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($filter === 'approved') {
            $query->where('approved', true);
        } elseif ($filter === 'pending') {
            $query->where('approved', false);
        }

        return $query->paginate(15)->withQueryString();
    }

    public function accepterUtilisateur(User $user): void
    {
        $user->update(['approved' => true]);
    }

    public function refuserUtilisateur(User $user): void
    {
        $user->update(['approved' => false]);
    }

    public function supprimerUtilisateur(User $user): void
    {
        $user->delete();
    }

    public function envoyerResetMdp(User $user): void
    {
        Password::sendResetLink(['email' => $user->email]);
    }
}
