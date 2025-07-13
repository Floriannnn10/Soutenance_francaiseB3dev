<?php

namespace Database\Seeders;

use App\Models\Etudiant;
use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;

class EtudiantsSeeder extends Seeder
{
    public function run(): void
    {
        $etudiants = [
            [
                'name' => 'Jean Dupont',
                'email' => 'jean.dupont@example.com',
                'numero_etudiant' => 'ETU001',
                'date_naissance' => '2000-05-15',
                'lieu_naissance' => 'Paris',
                'adresse' => '123 Rue de la Paix, Paris',
                'telephone' => '0123456789',
                'nationalite' => 'Française',
                'sexe' => 'M',
            ],
            [
                'name' => 'Marie Martin',
                'email' => 'marie.martin@example.com',
                'numero_etudiant' => 'ETU002',
                'date_naissance' => '1999-08-22',
                'lieu_naissance' => 'Lyon',
                'adresse' => '456 Avenue des Sciences, Lyon',
                'telephone' => '0987654321',
                'nationalite' => 'Française',
                'sexe' => 'F',
            ],
            [
                'name' => 'Pierre Durand',
                'email' => 'pierre.durand@example.com',
                'numero_etudiant' => 'ETU003',
                'date_naissance' => '2001-03-10',
                'lieu_naissance' => 'Marseille',
                'adresse' => '789 Boulevard de l\'Université, Marseille',
                'telephone' => '0555666777',
                'nationalite' => 'Française',
                'sexe' => 'M',
            ],
        ];

        $roleEtudiant = Role::where('nom', Role::ETUDIANT)->first();

        foreach ($etudiants as $etudiantData) {
            $user = User::create([
                'name' => $etudiantData['name'],
                'email' => $etudiantData['email'],
                'password' => bcrypt('password'),
                'role_id' => $roleEtudiant->id,
            ]);

            Etudiant::create([
                'utilisateur_id' => $user->id,
                'numero_etudiant' => $etudiantData['numero_etudiant'],
                'date_naissance' => $etudiantData['date_naissance'],
                'lieu_naissance' => $etudiantData['lieu_naissance'],
                'adresse' => $etudiantData['adresse'],
                'telephone' => $etudiantData['telephone'],
                'nationalite' => $etudiantData['nationalite'],
                'sexe' => $etudiantData['sexe'],
            ]);
        }
    }
}
