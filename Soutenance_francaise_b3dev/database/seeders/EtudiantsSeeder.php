<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Etudiant;
use App\Models\Classe;
use Illuminate\Support\Facades\DB;

class EtudiantsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roleEtudiant = Role::where('code', 'etudiant')->first();

        if (!$roleEtudiant) {
            throw new \Exception('Le rôle étudiant n\'existe pas. Veuillez exécuter le seeder RolesSeeder d\'abord.');
        }

        // Récupérer spécifiquement les classes M2 DEV
        $classes = Classe::whereIn('nom', ['M2 DEV A', 'M2 DEV B'])->get();

        if ($classes->isEmpty()) {
            throw new \Exception('Les classes M2 DEV n\'existent pas. Veuillez exécuter le seeder ClassesSeeder d\'abord.');
        }

        // Créer des étudiants spécifiques pour M2 DEV
        $etudiantsM2DEV = [
            // M2 DEV A - Étudiants
            ['prenom' => 'Alexandre', 'nom' => 'Dubois', 'classe' => 'M2 DEV A'],
            ['prenom' => 'Sarah', 'nom' => 'Lefevre', 'classe' => 'M2 DEV A'],
            ['prenom' => 'Maxime', 'nom' => 'Garcia', 'classe' => 'M2 DEV A'],
            ['prenom' => 'Léa', 'nom' => 'Rousseau', 'classe' => 'M2 DEV A'],
            ['prenom' => 'Hugo', 'nom' => 'Blanc', 'classe' => 'M2 DEV A'],
            ['prenom' => 'Chloé', 'nom' => 'Henry', 'classe' => 'M2 DEV A'],
            ['prenom' => 'Nathan', 'nom' => 'Garnier', 'classe' => 'M2 DEV A'],
            ['prenom' => 'Jade', 'nom' => 'Faure', 'classe' => 'M2 DEV A'],
            ['prenom' => 'Théo', 'nom' => 'Mercier', 'classe' => 'M2 DEV A'],
            ['prenom' => 'Zoé', 'nom' => 'Berger', 'classe' => 'M2 DEV A'],

            // M2 DEV B - Étudiants
            ['prenom' => 'Louis', 'nom' => 'Meyer', 'classe' => 'M2 DEV B'],
            ['prenom' => 'Inès', 'nom' => 'Leroy', 'classe' => 'M2 DEV B'],
            ['prenom' => 'Raphaël', 'nom' => 'Girard', 'classe' => 'M2 DEV B'],
            ['prenom' => 'Louise', 'nom' => 'Bonnet', 'classe' => 'M2 DEV B'],
            ['prenom' => 'Ethan', 'nom' => 'Dupuis', 'classe' => 'M2 DEV B'],
            ['prenom' => 'Alice', 'nom' => 'Lambert', 'classe' => 'M2 DEV B'],
            ['prenom' => 'Adam', 'nom' => 'Fontaine', 'classe' => 'M2 DEV B'],
            ['prenom' => 'Eva', 'nom' => 'Roussel', 'classe' => 'M2 DEV B'],
            ['prenom' => 'Liam', 'nom' => 'Vincent', 'classe' => 'M2 DEV B'],
            ['prenom' => 'Nina', 'nom' => 'Muller', 'classe' => 'M2 DEV B'],
        ];

        $etudiantCount = 0;

        foreach ($etudiantsM2DEV as $etudiant) {
            $classe = Classe::where('nom', $etudiant['classe'])->first();

            if (!$classe) {
                continue;
            }
                $timestamp = time() + $etudiantCount;
                $email = strtolower($etudiant['prenom'] . '.' . $etudiant['nom'] . $timestamp . '@example.com');

                // Vérifier si l'email existe déjà
                if (User::where('email', $email)->exists() || Etudiant::where('email', $email)->exists()) {
                    continue;
                }

                try {
                    // Créer l'utilisateur
                    $user = User::create([
                        'name' => $etudiant['prenom'] . ' ' . $etudiant['nom'],
                        'email' => $email,
                        'password' => bcrypt('password'),
                    ]);

                    // Associer le rôle étudiant
                    $user->roles()->attach($roleEtudiant->id);

                    // Créer l'étudiant
                    Etudiant::create([
                        'classe_id' => $classe->id,
                        'nom' => $etudiant['nom'],
                        'prenom' => $etudiant['prenom'],
                        'email' => $email,
                        'password' => bcrypt('password'),
                        'date_naissance' => now()->subYears(rand(18, 25))->format('Y-m-d'),
                        'photo' => null
                    ]);

                    $etudiantCount++;
                } catch (\Exception $e) {
                    continue;
                }
        }

        $this->command->info("{$etudiantCount} étudiants créés avec succès !");
    }
}
