<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\ParentEtudiant;
use App\Models\Etudiant;

class ParentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roleParent = Role::where('code', 'parent')->first();

        if (!$roleParent) {
            throw new \Exception('Le rôle parent n\'existe pas. Veuillez exécuter le seeder RolesSeeder d\'abord.');
        }

        $etudiants = Etudiant::all();

        if ($etudiants->isEmpty()) {
            throw new \Exception('Aucun étudiant n\'existe. Veuillez exécuter le seeder EtudiantsSeeder d\'abord.');
        }

        foreach ($etudiants as $etudiant) {
            // Créer un parent pour chaque étudiant
            $user = User::create([
                'nom' => $etudiant->nom,
                'prenom' => "Parent de {$etudiant->prenom}",
                'email' => "parent.{$etudiant->email}",
                'password' => bcrypt('password'),
            ]);

            $user->roles()->attach($roleParent->id);

            $parent = ParentEtudiant::create([
                'user_id' => $user->id,
                'prenom' => 'Parent',
                'nom' => $etudiant->nom,
                'telephone' => '+22507000000',
                'photo' => null
            ]);

            // Associer le parent à l'étudiant
            $parent->etudiants()->attach($etudiant->id);
        }
    }
}
