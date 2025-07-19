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
            // Calculer les dates des semestres
            $dateDebut = $annee->date_debut;
            $dateFin = $annee->date_fin;

            // Calculer la date de fin du premier semestre (milieu de l'année académique)
            $milieu = $dateDebut->copy()->addMonths(4); // Environ 4 mois pour le premier semestre

            // Semestre 1
            Semestre::updateOrCreate([
                'nom' => 'Semestre 1',
                'annee_academique_id' => $annee->id,
            ], [
                'date_debut' => $dateDebut,
                'date_fin' => $milieu,
                'actif' => $annee->actif && true, // Premier semestre actif si l'année est active
            ]);

            // Semestre 2
            Semestre::updateOrCreate([
                'nom' => 'Semestre 2',
                'annee_academique_id' => $annee->id,
            ], [
                'date_debut' => $milieu->copy()->addDay(),
                'date_fin' => $dateFin,
                'actif' => false, // Deuxième semestre inactif par défaut
            ]);
        }
    }
}
