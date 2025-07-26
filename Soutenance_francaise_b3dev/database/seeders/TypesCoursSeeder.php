<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TypeCours;

class TypesCoursSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            [
                'nom' => 'Présentiel',
                'code' => 'presentiel',
                'description' => 'Cours en présentiel'
            ],
            [
                'nom' => 'E-learning',
                'code' => 'e_learning',
                'description' => 'Cours en ligne'
            ],
            [
                'nom' => 'Workshop',
                'code' => 'workshop',
                'description' => 'Atelier pratique'
            ]
        ];

        foreach ($types as $type) {
            TypeCours::create($type);
        }
    }
}
