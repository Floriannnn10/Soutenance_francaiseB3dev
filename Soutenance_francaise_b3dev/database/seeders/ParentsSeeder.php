<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ParentEtudiant;
use App\Models\User;
use App\Models\Role;

class ParentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer un rôle parent s'il n'existe pas
        $roleParent = Role::where('nom', 'Parent')->first();

        // Créer des utilisateurs pour les parents
        $parentsData = [
            [
                'nom' => 'Dupont',
                'prenom' => 'Jean',
                'email' => 'jean.dupont@example.com',
                'telephone' => '0123456789',
            ],
            [
                'nom' => 'Martin',
                'prenom' => 'Marie',
                'email' => 'marie.martin@example.com',
                'telephone' => '0987654321',
            ],
            [
                'nom' => 'Durand',
                'prenom' => 'Pierre',
                'email' => 'pierre.durand@example.com',
                'telephone' => '0555666777',
            ],
            [
                'nom' => 'Leroy',
                'prenom' => 'Sophie',
                'email' => 'sophie.leroy@example.com',
                'telephone' => '0444555666',
            ],
            [
                'nom' => 'Moreau',
                'prenom' => 'Claude',
                'email' => 'claude.moreau@example.com',
                'telephone' => '0333444555',
            ],
        ];

        foreach ($parentsData as $parentData) {
            $user = User::create([
                'nom' => $parentData['nom'],
                'prenom' => $parentData['prenom'],
                'email' => $parentData['email'],
                'password' => bcrypt('password'),
                'role_id' => $roleParent->id,
            ]);

            ParentEtudiant::create([
                'user_id' => $user->id,
                'prenom' => $parentData['prenom'],
                'nom' => $parentData['nom'],
                'telephone' => $parentData['telephone'],
                'photo' => null,
            ]);
        }
    }
}
