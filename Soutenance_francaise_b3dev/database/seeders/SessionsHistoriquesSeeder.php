<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SessionsHistoriquesSeeder extends Seeder
{
    public function run(): void
    {
        echo "=== Création des sessions historiques pour les années passées ===\n";

        // Récupérer les années passées (exclure l'année courante)
        $anneesPassées = DB::table('annees_academiques')
            ->where('nom', '!=', '2024-2025')
            ->get();

        if ($anneesPassées->isEmpty()) {
            echo "❌ Aucune année passée trouvée\n";
            return;
        }

        // Récupérer les données nécessaires
        $matieres = DB::table('matieres')->get();
        $enseignants = DB::table('enseignants')->get();
        $typesCours = DB::table('types_cours')->whereIn('nom', ['Présentiel', 'E-learning', 'Workshop'])->get();
        $classes = DB::table('classes')->get();
        $statutsSession = DB::table('statuts_session')->get();
        $statutsPresence = DB::table('statuts_presence')->get();

        if ($matieres->isEmpty() || $enseignants->isEmpty() || $typesCours->isEmpty() || $classes->isEmpty()) {
            echo "❌ Données manquantes pour créer les sessions historiques\n";
            return;
        }

        foreach ($anneesPassées as $annee) {
            echo "\n--- Traitement de l'année {$annee->nom} ---\n";

            // Récupérer les semestres de cette année
            $semestres = DB::table('semestres')->where('annee_academique_id', $annee->id)->get();

            foreach ($semestres as $semestre) {
                echo "  --- Semestre {$semestre->nom} ---\n";

                // Créer des sessions pour chaque classe
                foreach ($classes as $classe) {
                    $this->createSessionsForClasse($classe, $semestre, $matieres, $enseignants, $typesCours, $statutsSession, $statutsPresence);
                }
            }
        }

        echo "\n=== Sessions historiques créées avec succès ===\n";
    }

    private function createSessionsForClasse($classe, $semestre, $matieres, $enseignants, $typesCours, $statutsSession, $statutsPresence)
    {
        // Créer 3-5 sessions par classe par semestre
        $nombreSessions = rand(3, 5);

        for ($i = 0; $i < $nombreSessions; $i++) {
            $matiere = $matieres->random();
            $enseignant = $enseignants->random();
            $typeCours = $typesCours->random();
            $statutSession = $statutsSession->where('nom', 'Terminée')->first() ?: $statutsSession->first();

            // Générer des dates aléatoires dans le semestre
            $dateDebut = Carbon::parse($semestre->date_debut);
            $dateFin = Carbon::parse($semestre->date_fin);
            $dateSession = $dateDebut->copy()->addDays(rand(0, $dateDebut->diffInDays($dateFin)));

            $startTime = $dateSession->copy()->setHour(rand(8, 16))->setMinute(0);
            $endTime = $startTime->copy()->addHours(rand(1, 3));

            // Créer la session
            $sessionId = DB::table('course_sessions')->insertGetId([
                'semester_id' => $semestre->id,
                'classe_id' => $classe->id,
                'matiere_id' => $matiere->id,
                'enseignant_id' => $enseignant->id,
                'type_cours_id' => $typeCours->id,
                'status_id' => $statutSession->id,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'location' => 'Salle ' . chr(65 + rand(0, 5)) . rand(100, 200),
                'notes' => 'Session historique de ' . $matiere->nom . ' - ' . $typeCours->nom,
                'annee_academique_id' => $semestre->annee_academique_id,
                'created_at' => $startTime,
                'updated_at' => $startTime,
            ]);

            // Créer des présences historiques pour cette session
            $this->createPresencesHistoriques($sessionId, $classe, $statutsPresence, $startTime);

            echo "    ✅ Session créée: {$matiere->nom} - {$typeCours->nom} ({$startTime->format('d/m/Y H:i')})\n";
        }
    }

    private function createPresencesHistoriques($sessionId, $classe, $statutsPresence, $dateSession)
    {
        // Récupérer les étudiants de cette classe
        $etudiants = DB::table('etudiants')->where('classe_id', $classe->id)->get();

        if ($etudiants->isEmpty()) {
            return;
        }

        $present = $statutsPresence->where('nom', 'Présent')->first();
        $absent = $statutsPresence->where('nom', 'Absent')->first();
        $justifie = $statutsPresence->where('nom', 'Justifié')->first();
        $retard = $statutsPresence->where('nom', 'Retard')->first();

        $statuts = [$present, $absent, $justifie, $retard];
        $defaultStatut = $statutsPresence->first();

        foreach ($etudiants as $etudiant) {
            // Répartition aléatoire des statuts de présence
            $statut = $statuts[array_rand($statuts)] ?: $defaultStatut;

            DB::table('presences')->insert([
                'etudiant_id' => $etudiant->id,
                'course_session_id' => $sessionId,
                'statut_presence_id' => $statut->id,
                'enregistre_le' => $dateSession,
                'enregistre_par_user_id' => 1, // Admin
                'created_at' => $dateSession,
                'updated_at' => $dateSession,
            ]);
        }
    }
}
