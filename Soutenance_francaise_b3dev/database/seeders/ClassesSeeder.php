<?php

namespace Database\Seeders;

use App\Models\Classe;
use Illuminate\Database\Seeder;

class ClassesSeeder extends Seeder
{
    public function run(): void
    {
        $classes = [
            [
                'nom' => 'L1 Informatique',
                'niveau' => 'Licence 1',
                'specialite' => 'Informatique',
                'description' => 'Première année de licence informatique',
            ],
            [
                'nom' => 'L2 Informatique',
                'niveau' => 'Licence 2',
                'specialite' => 'Informatique',
                'description' => 'Deuxième année de licence informatique',
            ],
            [
                'nom' => 'L3 Mathématiques',
                'niveau' => 'Licence 3',
                'specialite' => 'Mathématiques',
                'description' => 'Troisième année de licence mathématiques',
            ],
        ];
        foreach ($classes as $classe) {
            Classe::create($classe);
        }
    }
}
