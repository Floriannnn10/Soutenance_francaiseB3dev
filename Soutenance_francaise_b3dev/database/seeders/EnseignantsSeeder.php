<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Enseignant;
use App\Models\User;
use App\Models\Role;

class EnseignantsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer un rôle enseignant s'il n'existe pas
        $roleEnseignant = Role::where('nom', 'Enseignant')->first();

        // Créer des utilisateurs spécifiques pour les enseignants
        $enseignantsData = [
            [
                'nom' => 'Bernard',
                'prenom' => 'Sophie',
                'email' => 'sophie.bernard.ens@example.com',
            ],
            [
                'nom' => 'Dubois',
                'prenom' => 'Michel',
                'email' => 'michel.dubois.ens@example.com',
            ],
            [
                'nom' => 'Moreau',
                'prenom' => 'Claire',
                'email' => 'claire.moreau.ens@example.com',
            ],
            [
                'nom' => 'Leroy',
                'prenom' => 'Jean',
                'email' => 'jean.leroy.ens@example.com',
            ],
            [
                'nom' => 'Garcia',
                'prenom' => 'Maria',
                'email' => 'maria.garcia.ens@example.com',
            ],
        ];

        // Créer les enseignants spécifiques
        foreach (
            $enseignantsData as $enseignantData
        ) {
            Enseignant::create([
                'prenom' => $enseignantData['prenom'],
                'nom' => $enseignantData['nom'],
                'photo' => null,
            ]);
        }

        // Créer quelques enseignants supplémentaires aléatoires
        for ($i = 6; $i <= 10; $i++) {
            Enseignant::create([
                'prenom' => 'Enseignant' . $i,
                'nom' => 'Enseignant',
                'photo' => null,
            ]);
        }
    }
}
