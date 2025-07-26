<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'nom' => 'Administrateur',
                'code' => 'admin',
                'description' => 'Administrateur du système',
            ],
            [
                'nom' => 'Coordinateur',
                'code' => 'coordinateur',
                'description' => 'Coordinateur pédagogique',
            ],
            [
                'nom' => 'Enseignant',
                'code' => 'enseignant',
                'description' => 'Enseignant',
            ],
            [
                'nom' => 'Étudiant',
                'code' => 'etudiant',
                'description' => 'Étudiant',
            ],
            [
                'nom' => 'Parent',
                'code' => 'parent',
                'description' => 'Parent d\'étudiant',
            ],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
