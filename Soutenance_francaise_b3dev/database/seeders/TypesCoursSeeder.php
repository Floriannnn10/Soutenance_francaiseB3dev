<?php

namespace Database\Seeders;

use App\Models\TypeCours;
use Illuminate\Database\Seeder;

class TypesCoursSeeder extends Seeder
{
    public function run(): void
    {
        $typesCours = [
            [
                'nom' => TypeCours::CM,
                'description' => 'Cours Magistral',
            ],
            [
                'nom' => TypeCours::TD,
                'description' => 'Travaux Dirigés',
            ],
            [
                'nom' => TypeCours::TP,
                'description' => 'Travaux Pratiques',
            ],
            [
                'nom' => TypeCours::EXAMEN,
                'description' => 'Examen',
            ],
            [
                'nom' => TypeCours::CONTROLE,
                'description' => 'Contrôle',
            ],
        ];
        foreach ($typesCours as $type) {
            TypeCours::create($type);
        }
    }
}
