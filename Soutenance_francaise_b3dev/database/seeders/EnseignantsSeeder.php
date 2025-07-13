<?php

namespace Database\Seeders;

use App\Models\Enseignant;
use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;

class EnseignantsSeeder extends Seeder
{
    public function run(): void
    {
        $enseignants = [
            [
                'name' => 'Dr. Sophie Bernard',
                'email' => 'sophie.bernard@example.com',
                'numero_enseignant' => 'ENS001',
                'grade' => 'Maître de conférences',
                'specialite' => 'Informatique',
                'telephone' => '0123456789',
                'bureau' => 'Bâtiment A, Bureau 101',
            ],
            [
                'name' => 'Prof. Michel Dubois',
                'email' => 'michel.dubois@example.com',
                'numero_enseignant' => 'ENS002',
                'grade' => 'Professeur',
                'specialite' => 'Mathématiques',
                'telephone' => '0987654321',
                'bureau' => 'Bâtiment B, Bureau 205',
            ],
            [
                'name' => 'Dr. Claire Moreau',
                'email' => 'claire.moreau@example.com',
                'numero_enseignant' => 'ENS003',
                'grade' => 'Maître de conférences',
                'specialite' => 'Physique',
                'telephone' => '0555666777',
                'bureau' => 'Bâtiment C, Bureau 301',
            ],
        ];

        $roleEnseignant = Role::where('nom', Role::ENSEIGNANT)->first();

        foreach ($enseignants as $enseignantData) {
            $user = User::create([
                'name' => $enseignantData['name'],
                'email' => $enseignantData['email'],
                'password' => bcrypt('password'),
                'role_id' => $roleEnseignant->id,
            ]);

            Enseignant::create([
                'utilisateur_id' => $user->id,
                'numero_enseignant' => $enseignantData['numero_enseignant'],
                'grade' => $enseignantData['grade'],
                'specialite' => $enseignantData['specialite'],
                'telephone' => $enseignantData['telephone'],
                'bureau' => $enseignantData['bureau'],
            ]);
        }
    }
}
