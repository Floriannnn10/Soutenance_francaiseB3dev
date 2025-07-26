<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\Matiere;
use App\Models\Enseignant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EnseignantsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $matieres = Matiere::all();

        if ($matieres->isEmpty()) {
            throw new \Exception('Aucune matière n\'existe. Veuillez exécuter le seeder MatieresSeeder d\'abord.');
        }

        $enseignantRole = Role::where('code', 'enseignant')->first();

        if (!$enseignantRole) {
            throw new \Exception('Le rôle enseignant n\'existe pas. Veuillez exécuter le seeder RolesSeeder d\'abord.');
        }

        $enseignants = [
            [
                'nom' => 'Dupont',
                'prenom' => 'Jean',
                'email' => 'jean.dupont@example.com',
                'password' => 'password',
            ],
            [
                'nom' => 'Martin',
                'prenom' => 'Marie',
                'email' => 'marie.martin@example.com',
                'password' => 'password',
            ],
            [
                'nom' => 'Bernard',
                'prenom' => 'Pierre',
                'email' => 'pierre.bernard@example.com',
                'password' => 'password',
            ],
        ];

        foreach ($enseignants as $enseignantData) {
            $user = User::create([
                'name' => $enseignantData['prenom'] . ' ' . $enseignantData['nom'],
                'email' => $enseignantData['email'],
                'password' => Hash::make($enseignantData['password']),
            ]);

            $user->roles()->attach($enseignantRole->id);

            $enseignant = Enseignant::create([
                'nom' => $enseignantData['nom'],
                'prenom' => $enseignantData['prenom'],
                'user_id' => $user->id,
            ]);

            // Attribuer aléatoirement 2 matières à chaque enseignant
            $matieresAleatoires = $matieres->random(2);
            $enseignant->matieres()->attach($matieresAleatoires->pluck('id'));
        }
    }
}
