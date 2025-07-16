<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Etudiant;
use App\Models\User;
use App\Models\Classe;
use App\Models\Role;

class EtudiantsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer un rôle étudiant s'il n'existe pas
        $roleEtudiant = Role::where('nom', 'Étudiant')->first();

        // Créer des utilisateurs spécifiques pour les étudiants
        $etudiantsData = [
            [
                'nom' => 'Dupont',
                'prenom' => 'Thomas',
                'email' => 'thomas.dupont@example.com',
                'classe_nom' => 'Licence 1 Informatique',
            ],
            [
                'nom' => 'Martin',
                'prenom' => 'Emma',
                'email' => 'emma.martin@example.com',
                'classe_nom' => 'Licence 2 Informatique',
            ],
            [
                'nom' => 'Durand',
                'prenom' => 'Lucas',
                'email' => 'lucas.durand@example.com',
                'classe_nom' => 'Licence 3 Informatique',
            ],
            [
                'nom' => 'Leroy',
                'prenom' => 'Chloé',
                'email' => 'chloe.leroy@example.com',
                'classe_nom' => 'Master 1 Informatique',
            ],
            [
                'nom' => 'Moreau',
                'prenom' => 'Alexandre',
                'email' => 'alexandre.moreau@example.com',
                'classe_nom' => 'Master 2 Informatique',
            ],
        ];

        // Récupérer les classes
        $classes = Classe::all();

        // Créer les étudiants spécifiques
        foreach ($etudiantsData as $etudiantData) {
            $classe = $classes->where('nom', $etudiantData['classe_nom'])->first();
            if (!$classe) {
                $classe = $classes->first(); // Fallback
            }

            $user = User::create([
                'nom' => $etudiantData['nom'],
                'prenom' => $etudiantData['prenom'],
                'email' => $etudiantData['email'],
                'password' => bcrypt('password'),
                'role_id' => $roleEtudiant->id,
            ]);

            Etudiant::create([
                'user_id' => $user->id,
                'classe_id' => $classe->id,
                'prenom' => $etudiantData['prenom'],
                'nom' => $etudiantData['nom'],
                'date_naissance' => '2000-05-15',
                'photo' => null,
            ]);
        }

        // Créer quelques étudiants supplémentaires aléatoires
        for ($i = 6; $i <= 20; $i++) {
            $user = User::create([
                'nom' => 'Étudiant',
                'prenom' => 'Étudiant' . $i,
                'email' => 'etudiant' . $i . '@example.com',
                'password' => bcrypt('password'),
                'role_id' => $roleEtudiant->id,
            ]);

            $classe = $classes->random();

            Etudiant::create([
                'user_id' => $user->id,
                'classe_id' => $classe->id,
                'prenom' => $user->prenom,
                'nom' => $user->nom,
                'date_naissance' => '2000-05-15',
                'photo' => null,
            ]);
        }
    }
}
