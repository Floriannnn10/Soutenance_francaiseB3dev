<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AnneeAcademique;
use Carbon\Carbon;

class AnneesAcademiquesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $annees = [
            [
                'nom' => '2021-2022',
                'date_debut' => '2021-09-01',
                'date_fin' => '2022-08-31',
                'actif' => false
            ],
            [
                'nom' => '2022-2023',
                'date_debut' => '2022-09-01',
                'date_fin' => '2023-08-31',
                'actif' => false
            ],
            [
                'nom' => '2023-2024',
                'date_debut' => '2023-09-01',
                'date_fin' => '2024-08-31',
                'actif' => true
            ]
        ];

        foreach ($annees as $annee) {
            AnneeAcademique::create($annee);
        }
    }
}
