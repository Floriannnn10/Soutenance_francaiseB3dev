<?php

namespace Database\Seeders;

use App\Models\Matiere;
use Illuminate\Database\Seeder;

class MatieresSeeder extends Seeder
{
    public function run(): void
    {
        $matieres = [
            [
                'nom' => 'Mathématiques',
                'code' => 'MATH101',
                'description' => 'Cours de mathématiques fondamentales',
                'coefficient' => 3,
                'heures_cm' => 30,
                'heures_td' => 20,
                'heures_tp' => 0,
            ],
            [
                'nom' => 'Informatique',
                'code' => 'INFO101',
                'description' => 'Introduction à l\'informatique',
                'coefficient' => 4,
                'heures_cm' => 25,
                'heures_td' => 15,
                'heures_tp' => 20,
            ],
            [
                'nom' => 'Physique',
                'code' => 'PHYS101',
                'description' => 'Cours de physique générale',
                'coefficient' => 2,
                'heures_cm' => 20,
                'heures_td' => 15,
                'heures_tp' => 10,
            ],
        ];
        foreach ($matieres as $matiere) {
            Matiere::create($matiere);
        }
    }
}
