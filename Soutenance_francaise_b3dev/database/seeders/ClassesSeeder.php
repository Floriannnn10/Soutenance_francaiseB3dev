<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Classe;

class ClassesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classes = [
            ['nom' => 'Licence 1 Informatique'],
            ['nom' => 'Licence 2 Informatique'],
            ['nom' => 'Licence 3 Informatique'],
            ['nom' => 'Master 1 Informatique'],
            ['nom' => 'Master 2 Informatique'],
            ['nom' => 'Licence 1 Mathématiques'],
            ['nom' => 'Licence 2 Mathématiques'],
            ['nom' => 'Licence 3 Mathématiques'],
        ];

        foreach ($classes as $classe) {
            Classe::create($classe);
        }
    }
}
