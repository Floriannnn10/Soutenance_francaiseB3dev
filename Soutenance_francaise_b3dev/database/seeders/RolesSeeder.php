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
                'nom' => Role::ADMIN,
                'description' => 'Administrateur du système avec tous les droits',
            ],
            [
                'nom' => Role::COORDINATEUR,
                'description' => 'Coordinateur pédagogique avec droits étendus',
            ],
            [
                'nom' => Role::ENSEIGNANT,
                'description' => 'Enseignant avec droits limités à ses cours',
            ],
            [
                'nom' => Role::ETUDIANT,
                'description' => 'Étudiant avec accès en lecture seule',
            ],
            [
                'nom' => Role::PARENT,
                'description' => 'Parent avec accès aux informations de son enfant',
            ],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
