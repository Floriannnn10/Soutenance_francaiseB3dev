<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['nom' => 'Admin'],
            ['nom' => 'Coordinateur'],
            ['nom' => 'Enseignant'],
            ['nom' => 'Étudiant'],
            ['nom' => 'Parent'],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
