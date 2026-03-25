<?php

namespace Database\Seeders;

use App\Models\Parking;
use App\Models\Place;
use App\Models\User;
use App\Models\Voiture;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{

    public function run(): void
    {
        $user  = $this->creerUtilisateur();
        $admin = $this->creerAdmin();

        $this->creerVoitures($user);

        $parking = $this->creerParking();
        $this->creerPlace($parking);
    }

    private function creerUtilisateur(): User
    {
        return User::create([
            'name'              => 'User',
            'prenom'            => 'baka',
            'email'             => 'user@gmail.com',
            'email_verified_at' => now(),
            'password'          => Hash::make('Usercompte2004.'),
            'role'              => 'user',
        ]);
    }

    private function creerAdmin(): User
    {
        return User::create([
            'name'              => 'Admin',
            'prenom'            => 'Super',
            'email'             => 'admin@gmail.com',
            'email_verified_at' => now(),
            'password'          => Hash::make('Admincompte2004.'),
            'role'              => 'admin',
        ]);
    }

    private function creerVoitures(User $user): void
    {
        $voitures = [
            ['modele_voiture' => 'Renault Clio',   'immatriculation' => 'AB-123-CD'],
            ['modele_voiture' => 'Peugeot 308',    'immatriculation' => 'EF-456-GH'],
            ['modele_voiture' => 'Citroën C3',     'immatriculation' => 'IJ-789-KL'],
        ];

        foreach ($voitures as $donnees) {
            Voiture::create([
                'modele_voiture' => $donnees['modele_voiture'],
                'immatriculation' => $donnees['immatriculation'],
                'user_id'         => $user->id,
            ]);
        }
    }

    private function creerParking(): Parking
    {
        return Parking::create([
            'ville_parking' => 'Mery',
            'lib_parking'   => 'Parking Central',
        ]);
    }

    private function creerPlace(Parking $parking): void
    {
        Place::create([
            'num_place'  => '1',
            'disponible' => true,
            'parking_id' => $parking->id,
        ]);
    }
}
