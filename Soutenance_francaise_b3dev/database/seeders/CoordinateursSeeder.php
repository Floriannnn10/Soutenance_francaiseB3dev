<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Coordinateur;
use App\Models\User;
use App\Models\Role;

class CoordinateursSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer un rôle coordinateur s'il n'existe pas
        $roleCoordinateur = Role::where('nom', 'Coordinateur')->first();

        // Créer des utilisateurs pour les coordinateurs
        $coordinateursData = [
            [
                'nom' => 'Bernard',
                'prenom' => 'Sophie',
                'email' => 'sophie.bernard.coord@example.com',
            ],
            [
                'nom' => 'Dubois',
                'prenom' => 'Michel',
                'email' => 'michel.dubois.coord@example.com',
            ],
            [
                'nom' => 'Moreau',
                'prenom' => 'Claire',
                'email' => 'claire.moreau.coord@example.com',
            ],
        ];

        foreach ($coordinateursData as $coordinateurData) {
            $user = User::create([
                'nom' => $coordinateurData['nom'],
                'prenom' => $coordinateurData['prenom'],
                'email' => $coordinateurData['email'],
                'password' => bcrypt('password'),
                'role_id' => $roleCoordinateur->id,
            ]);

            Coordinateur::create([
                'user_id' => $user->id,
                'prenom' => $coordinateurData['prenom'],
                'nom' => $coordinateurData['nom'],
                'photo' => null,
            ]);
        }
    }
}
