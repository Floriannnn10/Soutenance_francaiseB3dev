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

        if ($annees->isEmpty()) {
            throw new \Exception('Aucune année académique n\'existe. Veuillez exécuter le seeder AnneesAcademiquesSeeder d\'abord.');
        }

        foreach ($annees as $annee) {
            // Premier semestre
            Semestre::create([
                'nom' => 'Semestre 1',
                'annee_academique_id' => $annee->id,
                'date_debut' => $annee->date_debut,
                'date_fin' => date('Y-m-d', strtotime($annee->date_debut . ' + 5 months')),
                'actif' => $annee->actif
            ]);

            // Deuxième semestre
            Semestre::create([
                'nom' => 'Semestre 2',
                'annee_academique_id' => $annee->id,
                'date_debut' => date('Y-m-d', strtotime($annee->date_debut . ' + 6 months')),
                'date_fin' => $annee->date_fin,
                'actif' => false
            ]);
        }
    }
}
