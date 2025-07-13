<?php

namespace Database\Seeders;

use App\Models\AnneeAcademique;
use App\Models\Semestre;
use Illuminate\Database\Seeder;

class AnneesAcademiquesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Année académique 2024-2025
        $annee2024 = AnneeAcademique::create([
            'nom' => '2024-2025',
            'date_debut' => '2024-09-01',
            'date_fin' => '2025-08-31',
            'est_active' => true,
        ]);

        // Semestres pour 2024-2025
        $semestres = [
            [
                'nom' => 'Semestre 1',
                'date_debut' => '2024-09-01',
                'date_fin' => '2025-01-31',
            ],
            [
                'nom' => 'Semestre 2',
                'date_debut' => '2025-02-01',
                'date_fin' => '2025-06-30',
            ],
        ];

        foreach ($semestres as $semestre) {
            Semestre::create([
                'annee_academique_id' => $annee2024->id,
                'nom' => $semestre['nom'],
                'date_debut' => $semestre['date_debut'],
                'date_fin' => $semestre['date_fin'],
            ]);
        }

        // Année académique 2023-2024 (inactive)
        $annee2023 = AnneeAcademique::create([
            'nom' => '2023-2024',
            'date_debut' => '2023-09-01',
            'date_fin' => '2024-08-31',
            'est_active' => false,
        ]);

        // Semestres pour 2023-2024
        $semestres2023 = [
            [
                'nom' => 'Semestre 1',
                'date_debut' => '2023-09-01',
                'date_fin' => '2024-01-31',
            ],
            [
                'nom' => 'Semestre 2',
                'date_debut' => '2024-02-01',
                'date_fin' => '2024-06-30',
            ],
        ];

        foreach ($semestres2023 as $semestre) {
            Semestre::create([
                'annee_academique_id' => $annee2023->id,
                'nom' => $semestre['nom'],
                'date_debut' => $semestre['date_debut'],
                'date_fin' => $semestre['date_fin'],
            ]);
        }
    }
}
