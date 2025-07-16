<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer un rôle admin s'il n'existe pas
        $roleAdmin = Role::where('nom', 'Admin')->first();

        // Créer l'administrateur
        User::create([
            'nom' => 'Admin',
            'prenom' => 'Administrateur',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role_id' => $roleAdmin->id,
        ]);
    }
}
