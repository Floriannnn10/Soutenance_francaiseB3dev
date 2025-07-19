<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AnneeAcademique;

class AnneesAcademiquesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $annees = [
            [
                'nom' => '2024-2025',
                'date_debut' => '2024-09-01',
                'date_fin' => '2025-08-31',
                'actif' => true,
            ],
            [
                'nom' => '2023-2024',
                'date_debut' => '2023-09-01',
                'date_fin' => '2024-08-31',
                'actif' => false,
            ],
            [
                'nom' => '2025-2026',
                'date_debut' => '2025-09-01',
                'date_fin' => '2026-08-31',
                'actif' => false,
            ],
        ];

        foreach ($annees as $annee) {
            AnneeAcademique::updateOrCreate(
                ['nom' => $annee['nom']],
                $annee
            );
        }
    }
}
