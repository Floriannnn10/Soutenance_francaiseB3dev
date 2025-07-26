<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Etudiant;
use App\Models\Classe;

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

        foreach ($classes as $classe) {
            for ($i = 1; $i <= 20; $i++) {
                $user = User::create([
                    'name' => "Étudiant {$i} {$classe->nom}",
                    'email' => "etudiant{$i}.{$classe->nom}@example.com",
                    'password' => bcrypt('password'),
                    'role_id' => $roleEtudiant->id
                ]);

                Etudiant::create([
                    'classe_id' => $classe->id,
                    'prenom' => "Étudiant {$i}",
                    'nom' => $classe->nom,
                    'email' => $user->email,
                    'password' => $user->password,
                    'date_naissance' => now()->subYears(rand(18, 25))->format('Y-m-d'),
                    'photo' => null
                ]);
            }
        }
    }
}
