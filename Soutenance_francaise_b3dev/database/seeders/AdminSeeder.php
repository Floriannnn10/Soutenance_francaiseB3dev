<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::where('code', 'admin')->first();

        if (!$adminRole) {
            throw new \Exception('Le rôle admin n\'existe pas. Veuillez exécuter le seeder RolesSeeder d\'abord.');
        }

        $admin = User::create([
            'nom' => 'Admin',
            'prenom' => 'Système',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        $admin->roles()->attach($adminRole->id);
    }
}
