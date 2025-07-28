<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SessionsAnneeCouranteSeeder extends Seeder
{
    public function run(): void
    {
        echo "=== Création des sessions pour l'année en cours (2024-2025) ===\n";

        // Récupérer l'année en cours
        $anneeCourante = DB::table('annees_academiques')
            ->where('nom', '2024-2025')
            ->first();

        if (!$anneeCourante) {
            echo "❌ Année 2024-2025 non trouvée. Création de l'année...\n";
            $anneeCouranteId = DB::table('annees_academiques')->insertGetId([
                'nom' => '2024-2025',
                'date_debut' => '2024-09-01',
                'date_fin' => '2025-08-31',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            echo "✅ Année 2024-2025 créée avec l'ID: {$anneeCouranteId}\n";
        } else {
            $anneeCouranteId = $anneeCourante->id;
            echo "✅ Année 2024-2025 trouvée avec l'ID: {$anneeCouranteId}\n";
        }

        // Récupérer les semestres de l'année en cours
        $semestres = DB::table('semestres')
            ->where('annee_academique_id', $anneeCouranteId)
            ->get();

        if ($semestres->isEmpty()) {
            echo "❌ Aucun semestre trouvé pour l'année 2024-2025. Création des semestres...\n";
            $semestre1Id = DB::table('semestres')->insertGetId([
                'nom' => 'Semestre 1',
                'annee_academique_id' => $anneeCouranteId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $semestre2Id = DB::table('semestres')->insertGetId([
                'nom' => 'Semestre 2',
                'annee_academique_id' => $anneeCouranteId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            echo "✅ Semestres créés: S1 (ID: {$semestre1Id}), S2 (ID: {$semestre2Id})\n";
        } else {
            echo "✅ Semestres trouvés: " . $semestres->count() . " semestres\n";
        }

        // Récupérer les données nécessaires
        $classes = DB::table('classes')->get();
        $matieres = DB::table('matieres')->get();
        $enseignants = DB::table('enseignants')->get();
        $typesCours = DB::table('types_cours')->get();
        $statutsSession = DB::table('statuts_session')->get();

        if ($classes->isEmpty() || $matieres->isEmpty() || $enseignants->isEmpty() || $typesCours->isEmpty() || $statutsSession->isEmpty()) {
            echo "❌ Données manquantes pour créer les sessions\n";
            return;
        }

        // Créer des sessions pour l'année en cours avec des données différentes
        $sessionsCreees = 0;
        foreach ($semestres as $semestre) {
            echo "\n--- Création de sessions pour le semestre: {$semestre->nom} ---\n";

            foreach ($classes as $classe) {
                // Créer 2-3 sessions par classe pour l'année en cours
                $nombreSessions = rand(2, 3);

                for ($i = 0; $i < $nombreSessions; $i++) {
                    $matiere = $matieres->random();
                    $enseignant = $enseignants->random();
                    $typeCours = $typesCours->random();
                    $statut = $statutsSession->random();

                    // Dates futures pour l'année en cours (différentes des années passées)
                    $dateDebut = Carbon::now()->addDays(rand(1, 30));
                    $dateFin = $dateDebut->copy()->addHours(2);

                    $sessionId = DB::table('course_sessions')->insertGetId([
                        'classe_id' => $classe->id,
                        'matiere_id' => $matiere->id,
                        'enseignant_id' => $enseignant->id,
                        'semester_id' => $semestre->id,
                        'annee_academique_id' => $anneeCouranteId,
                        'type_cours_id' => $typeCours->id,
                        'status_id' => $statut->id,
                        'start_time' => $dateDebut,
                        'end_time' => $dateFin,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    echo "✅ Session créée: {$matiere->nom} - {$classe->nom} - {$dateDebut->format('d/m/Y H:i')}\n";
                    $sessionsCreees++;

                    // Créer des présences pour cette session
                    $this->createPresencesForSession($sessionId, $classe->id);
                }
            }
        }

        echo "\n=== Sessions de l'année en cours créées avec succès ===\n";
        echo "📊 Total: {$sessionsCreees} sessions créées\n";
    }

    private function createPresencesForSession($sessionId, $classeId)
    {
        // Récupérer les étudiants de cette classe
        $etudiants = DB::table('etudiants')
            ->where('classe_id', $classeId)
            ->get();

        if ($etudiants->isEmpty()) {
            return;
        }

        // Récupérer ou créer les statuts de présence
        $statutsPresence = [];
        $statutsNoms = ['Présent', 'Absent', 'Retard', 'Justifié'];

        foreach ($statutsNoms as $nom) {
            $statut = DB::table('statuts_presence')->where('nom', $nom)->first();
            if (!$statut) {
                $statutId = DB::table('statuts_presence')->insertGetId([
                    'nom' => $nom,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $statutId = $statut->id;
            }
            $statutsPresence[] = $statutId;
        }

        // Créer des présences pour chaque étudiant
        foreach ($etudiants as $etudiant) {
            $statutPresenceId = $statutsPresence[array_rand($statutsPresence)];

            DB::table('presences')->insert([
                'course_session_id' => $sessionId,
                'etudiant_id' => $etudiant->id,
                'statut_presence_id' => $statutPresenceId,
                'enregistre_le' => Carbon::now(),
                'enregistre_par_user_id' => 1, // Admin
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
