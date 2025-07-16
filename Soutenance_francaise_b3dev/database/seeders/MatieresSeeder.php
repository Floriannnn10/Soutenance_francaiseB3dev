<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Matiere;

class MatieresSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $matieres = [
            ['nom' => 'Programmation Java'],
            ['nom' => 'Base de données'],
            ['nom' => 'Réseaux informatiques'],
            ['nom' => 'Algorithmes et structures de données'],
            ['nom' => 'Mathématiques discrètes'],
            ['nom' => 'Calcul différentiel'],
            ['nom' => 'Statistiques'],
            ['nom' => 'Intelligence artificielle'],
            ['nom' => 'Développement web'],
            ['nom' => 'Systèmes d\'exploitation'],
        ];

        foreach ($matieres as $matiere) {
            Matiere::create($matiere);
        }
    }
}
