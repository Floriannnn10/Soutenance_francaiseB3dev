<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Semestre;
use App\Models\AnneeAcademique;

class SemestresSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $annees = AnneeAcademique::all();

        foreach ($annees as $annee) {
            // Semestre 1
            Semestre::create([
                'libelle' => 'Semestre 1',
                'academic_year_id' => $annee->id,
                'date_debut' => $annee->date_debut,
                'date_fin' => date('Y-01-31', strtotime($annee->date_fin)),
                'actif' => $annee->actif,
            ]);

            // Semestre 2
            Semestre::create([
                'libelle' => 'Semestre 2',
                'academic_year_id' => $annee->id,
                'date_debut' => date('Y-02-01', strtotime($annee->date_fin)),
                'date_fin' => $annee->date_fin,
                'actif' => $annee->actif,
            ]);
        }
    }
}
