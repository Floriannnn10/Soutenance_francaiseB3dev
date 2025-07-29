<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\SessionDeCours;
use App\Models\Presence;
use App\Models\Etudiant;
use App\Models\Classe;
use App\Models\Coordinateur;
use App\Models\Enseignant;
use Carbon\Carbon;

class SessionsWithPresenceSeeder extends Seeder
{
    public function run(): void
    {
        // Nettoyer les sessions et présences existantes
        DB::table('presences')->delete();
        DB::table('course_sessions')->delete();

        // Récupérer les données nécessaires
        $matieres = DB::table('matieres')->limit(3)->get();
        $enseignants = DB::table('enseignants')->limit(3)->get();
        $typesCours = DB::table('types_cours')->whereIn('nom', ['Présentiel', 'E-learning', 'Workshop'])->get();
        $semestres = DB::table('semestres')->limit(1)->get();
        $classes = Classe::with('promotion')->get();
        $coordinateurs = Coordinateur::with('user')->get();

        if ($matieres->isEmpty() || $enseignants->isEmpty() || $typesCours->isEmpty() || $semestres->isEmpty()) {
            echo "❌ Données manquantes pour créer les sessions\n";
            return;
        }

        $semestre = $semestres->first();
        $anneeAcademique = DB::table('annees_academiques')->where('id', $semestre->annee_academique_id)->first();

        foreach ($classes as $classe) {
            $this->createSessionsForClass($classe, $matieres, $enseignants, $typesCours, $semestre, $anneeAcademique, $coordinateurs);
        }

        echo "✅ Sessions et présences créées avec succès !\n";
    }

    private function createSessionsForClass($classe, $matieres, $enseignants, $typesCours, $semestre, $anneeAcademique, $coordinateurs)
    {
        // Trouver le coordinateur de cette classe
        $coordinateur = $coordinateurs->where('promotion_id', $classe->promotion_id)->first();

        // Créer 5 sessions de cours pour cette classe
        for ($i = 0; $i < 5; $i++) {
            $matiere = $matieres->random();
            $enseignant = $enseignants->random();
            $typeCours = $typesCours->random();

            // Pour Workshop et E-learning, utiliser le coordinateur comme enseignant
            if (in_array($typeCours->nom, ['Workshop', 'E-learning']) && $coordinateur) {
                $enseignantCoordinateur = Enseignant::where('user_id', $coordinateur->user_id)->first();
                if ($enseignantCoordinateur) {
                    $enseignant = $enseignantCoordinateur;
                }
            }

            $startTime = Carbon::now()->addDays($i * 2)->setHour(8)->setMinute(0);
            $endTime = $startTime->copy()->addHours(2);

            // Récupérer ou créer le statut de session
            $statutSession = DB::table('statuts_session')->where('nom', 'Programmée')->first();
            if (!$statutSession) {
                // Créer le statut s'il n'existe pas
                $statutId = DB::table('statuts_session')->insertGetId([
                    'nom' => 'Programmée',
                    'code' => 'programmee',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $statutId = $statutSession->id;
            }

            $session = SessionDeCours::create([
                'semester_id' => $semestre->id,
                'classe_id' => $classe->id,
                'matiere_id' => $matiere->id,
                'enseignant_id' => $enseignant->id,
                'type_cours_id' => $typeCours->id,
                'status_id' => $statutId,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'location' => 'Salle ' . chr(65 + $i) . (100 + $i),
                'notes' => 'Session de ' . $matiere->nom . ' - ' . $typeCours->nom,
                'annee_academique_id' => $anneeAcademique->id,
            ]);

            // Créer des présences pour cette session
            $this->createPresencesForSession($session, $classe);
        }

        echo "✅ {$classe->nom} : 5 sessions créées avec présences\n";
    }

    private function createPresencesForSession($session, $classe)
    {
        $etudiants = Etudiant::where('classe_id', $classe->id)->get();
        $statutsPresence = DB::table('statuts_presence')->get();

        // Créer les statuts de présence s'ils n'existent pas
        if ($statutsPresence->isEmpty()) {
            $statutsData = [
                ['nom' => 'Présent', 'created_at' => now(), 'updated_at' => now()],
                ['nom' => 'Absent', 'created_at' => now(), 'updated_at' => now()],
                ['nom' => 'Justifié', 'created_at' => now(), 'updated_at' => now()],
                ['nom' => 'Retard', 'created_at' => now(), 'updated_at' => now()],
            ];

            foreach ($statutsData as $statutData) {
                DB::table('statuts_presence')->insert($statutData);
            }

            $statutsPresence = DB::table('statuts_presence')->get();
        }

        $present = $statutsPresence->where('nom', 'Présent')->first();
        $absent = $statutsPresence->where('nom', 'Absent')->first();
        $justifie = $statutsPresence->where('nom', 'Justifié')->first();
        $retard = $statutsPresence->where('nom', 'Retard')->first();

        // Utiliser le premier statut disponible si un statut spécifique n'existe pas
        $defaultStatut = $statutsPresence->first();

        foreach ($etudiants as $etudiant) {
            // Répartition aléatoire des statuts de présence
            $statuts = [$present ?: $defaultStatut, $absent ?: $defaultStatut, $justifie ?: $defaultStatut, $retard ?: $defaultStatut];
            $statut = $statuts[array_rand($statuts)];

            Presence::create([
                'etudiant_id' => $etudiant->id,
                'course_session_id' => $session->id,
                'statut_presence_id' => $statut->id,
                'enregistre_le' => Carbon::now(),
                'enregistre_par_user_id' => 1, // Admin
            ]);
        }
    }
}
