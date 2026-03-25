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
        $this->creerUsersTest();

        $this->creerVoitures($user);

        $parking = $this->creerParking();
        $this->creerPlace($parking);
    }

    private function creerUtilisateur(): User
    {
        return User::create([
            'name'              => 'User',
            'prenom'            => 'Baka',
            'email'             => 'user@gmail.com',
            'email_verified_at' => now(),
            'password'          => Hash::make('Usercompte2004.'),
            'role'              => 'user',
            'approved'          => true,
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
            'approved'          => true,
        ]);
    }

    private function creerUsersTest(): void
    {
        $users = [
            [
                'name'              => 'Dupont',
                'prenom'            => 'Jean',
                'email'             => 'jean.dupont@gmail.com',
                'password'          => Hash::make('Testcompte2004.'),
                'role'              => 'user',
                'approved'          => false,
            ],
            [
                'name'              => 'Martin',
                'prenom'            => 'Sophie',
                'email'             => 'sophie.martin@gmail.com',
                'password'          => Hash::make('Testcompte2004.'),
                'role'              => 'user',
                'approved'          => false,
            ],
            [
                'name'              => 'Bernard',
                'prenom'            => 'Lucas',
                'email'             => 'lucas.bernard@gmail.com',
                'password'          => Hash::make('Testcompte2004.'),
                'role'              => 'user',
                'approved'          => false,
            ],
        ];

        foreach ($users as $donnees) {
            User::create(array_merge($donnees, ['email_verified_at' => now()]));
        }
    }

    private function creerVoitures(User $user): void
    {
        $voitures = [
            ['modele_voiture' => 'Opel Corsa',  'immatriculation' => '1'],
            ['modele_voiture' => 'Peugeot 3008',   'immatriculation' => '2'],
            ['modele_voiture' => 'Kawasaki Ninja H2R',    'immatriculation' => '3'],
        ];

        foreach ($voitures as $donnees) {
            Voiture::create([
                'modele_voiture'  => $donnees['modele_voiture'],
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
