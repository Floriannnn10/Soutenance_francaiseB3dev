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
            [
                'code' => 'JAVA',
                'nom' => 'Programmation Java',
                'coefficient' => 3.0,
                'volume_horaire' => 60
            ],
            [
                'code' => 'PHP',
                'nom' => 'Développement Web PHP',
                'coefficient' => 3.0,
                'volume_horaire' => 60
            ],
            [
                'code' => 'JS',
                'nom' => 'JavaScript et Frameworks',
                'coefficient' => 2.5,
                'volume_horaire' => 45
            ],
            [
                'code' => 'PYTHON',
                'nom' => 'Python pour le Data Science',
                'coefficient' => 2.5,
                'volume_horaire' => 45
            ],
            [
                'code' => 'DEVOPS',
                'nom' => 'DevOps et CI/CD',
                'coefficient' => 2.0,
                'volume_horaire' => 30
            ],
            [
                'code' => 'SECU',
                'nom' => 'Sécurité des applications',
                'coefficient' => 2.0,
                'volume_horaire' => 30
            ]
        ];

        foreach ($matieres as $matiere) {
            Matiere::create($matiere);
        }
    }
}
