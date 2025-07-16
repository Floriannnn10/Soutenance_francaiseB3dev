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
        $typesCours = [
            ['nom' => 'CM'],
            ['nom' => 'TD'],
            ['nom' => 'TP'],
            ['nom' => 'Examen'],
            ['nom' => 'Contrôle'],
        ];

        foreach ($typesCours as $type) {
            TypeCours::create($type);
        }
    }
}
