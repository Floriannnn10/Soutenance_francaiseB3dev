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
            ['nom' => 'admin'],
            ['nom' => 'coordinateur'],
            ['nom' => 'enseignant'],
            ['nom' => 'étudiant'],
            ['nom' => 'parent'],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
