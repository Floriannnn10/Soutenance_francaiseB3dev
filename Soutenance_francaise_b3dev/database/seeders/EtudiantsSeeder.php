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

        $classes = Classe::all();

        if ($classes->isEmpty()) {
            throw new \Exception('Aucune classe n\'existe. Veuillez exécuter le seeder ClassesSeeder d\'abord.');
        }

        // Créer quelques étudiants de test pour chaque classe
        $etudiantsTest = [
            ['prenom' => 'Jean', 'nom' => 'Dupont'],
            ['prenom' => 'Marie', 'nom' => 'Martin'],
            ['prenom' => 'Pierre', 'nom' => 'Bernard'],
            ['prenom' => 'Sophie', 'nom' => 'Petit'],
            ['prenom' => 'Paul', 'nom' => 'Robert'],
            ['prenom' => 'Julie', 'nom' => 'Richard'],
            ['prenom' => 'Thomas', 'nom' => 'Durand'],
            ['prenom' => 'Camille', 'nom' => 'Leroy'],
            ['prenom' => 'Lucas', 'nom' => 'Moreau'],
            ['prenom' => 'Emma', 'nom' => 'Simon'],
        ];

        $etudiantCount = 0;

        foreach ($classes as $classe) {
            foreach ($etudiantsTest as $index => $etudiant) {
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
        }

        $this->command->info("{$etudiantCount} étudiants créés avec succès !");
    }
}
