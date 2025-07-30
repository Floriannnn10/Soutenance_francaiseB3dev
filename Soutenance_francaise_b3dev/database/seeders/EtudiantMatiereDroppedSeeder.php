<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EtudiantMatiereDropped;
use App\Models\Etudiant;
use App\Models\Matiere;
use App\Models\AnneeAcademique;
use App\Models\Semestre;
use App\Models\User;

class EtudiantMatiereDroppedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🌱 Création des exemples d\'étudiants ayant abandonné des matières...');

        // Récupérer les données nécessaires
        $etudiants = Etudiant::take(5)->get();
        $matieres = Matiere::take(3)->get();
        $anneeAcademique = AnneeAcademique::first();
        $semestres = Semestre::get();
        $coordinateur = User::whereHas('roles', function($query) {
            $query->where('code', 'coordinateur');
        })->first();

        if (!$etudiants->count() || !$matieres->count() || !$anneeAcademique || !$semestres->count()) {
            $this->command->warn('⚠️  Données insuffisantes pour créer les exemples. Assurez-vous d\'avoir des étudiants, matières, années académiques et semestres.');
            return;
        }

        $raisons = [
            'Difficultés académiques',
            'Changement d\'orientation',
            'Problèmes personnels',
            'Charge de travail trop importante',
            'Incompatibilité avec l\'emploi du temps',
            'Problèmes de santé',
            'Déménagement',
            'Autres raisons personnelles'
        ];

        $count = 0;

        foreach ($etudiants as $etudiant) {
            foreach ($matieres as $matiere) {
                // 30% de chance qu'un étudiant abandonne une matière
                if (rand(1, 100) <= 30) {
                    $semestre = $semestres->random();

                    EtudiantMatiereDropped::create([
                        'etudiant_id' => $etudiant->id,
                        'matiere_id' => $matiere->id,
                        'annee_academique_id' => $anneeAcademique->id,
                        'semestre_id' => $semestre->id,
                        'raison_drop' => $raisons[array_rand($raisons)],
                        'date_drop' => now()->subDays(rand(1, 90)),
                        'dropped_by' => $coordinateur ? $coordinateur->id : null,
                    ]);

                    $count++;
                    $this->command->info("✅ {$etudiant->prenom} {$etudiant->nom} a abandonné {$matiere->nom}");
                }
            }
        }

        $this->command->info("🎉 {$count} exemples d'abandons de matières ont été créés !");
    }
}
