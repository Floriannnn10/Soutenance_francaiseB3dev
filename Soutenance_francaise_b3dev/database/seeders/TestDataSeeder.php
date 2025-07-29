<?php

namespace Database\Seeders;

use App\Models\SessionDeCours;
use App\Models\Presence;
use App\Models\Etudiant;
use App\Models\Enseignant;
use App\Models\Classe;
use App\Models\Matiere;
use App\Models\TypeCours;
use App\Models\StatutSession;
use App\Models\StatutPresence;
use App\Models\Semestre;
use App\Models\AnneeAcademique;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Création des données de test...');

        // Créer des sessions de cours de test
        $this->createTestSessions();

        // Créer des présences de test
        $this->createTestPresences();

        $this->command->info('✅ Toutes les données de test ont été créées avec succès!');
    }

    private function createTestSessions()
    {
        $enseignants = Enseignant::all();
        $classes = Classe::all();
        $matieres = Matiere::all();
        $typesCours = TypeCours::all();
        $statutsSession = StatutSession::all();
        $semestres = Semestre::all();
        $anneeAcademique = AnneeAcademique::getActive() ?? AnneeAcademique::first();

        if ($enseignants->isEmpty() || $classes->isEmpty() || $matieres->isEmpty()) {
            $this->command->warn('Données insuffisantes pour créer des sessions de test.');
            return;
        }

        $sessions = [
            [
                'matiere' => 'Développement Web',
                'type' => 'Présentiel',
                'statut' => 'Programmée',
                'start_time' => Carbon::now()->addDays(1)->setHour(8)->setMinute(0),
                'end_time' => Carbon::now()->addDays(1)->setHour(10)->setMinute(0),
                'location' => 'Salle A101',
                'notes' => 'Cours sur les frameworks modernes',
            ],
            [
                'matiere' => 'Base de données',
                'type' => 'Présentiel',
                'statut' => 'En cours',
                'start_time' => Carbon::now()->setHour(14)->setMinute(0),
                'end_time' => Carbon::now()->setHour(16)->setMinute(0),
                'location' => 'Salle B202',
                'notes' => 'Introduction aux bases de données relationnelles',
            ],
            [
                'matiere' => 'Sécurité informatique',
                'type' => 'E-learning',
                'statut' => 'Terminée',
                'start_time' => Carbon::now()->subDays(1)->setHour(10)->setMinute(0),
                'end_time' => Carbon::now()->subDays(1)->setHour(12)->setMinute(0),
                'location' => 'Plateforme en ligne',
                'notes' => 'Module sur la cybersécurité',
            ],
            [
                'matiere' => 'Intelligence artificielle',
                'type' => 'Workshop',
                'statut' => 'Planifiée',
                'start_time' => Carbon::now()->addDays(3)->setHour(9)->setMinute(0),
                'end_time' => Carbon::now()->addDays(3)->setHour(11)->setMinute(0),
                'location' => 'Labo IA',
                'notes' => 'Atelier pratique sur le machine learning',
            ],
        ];

        foreach ($sessions as $sessionData) {
            $matiere = $matieres->where('nom', $sessionData['matiere'])->first() ?? $matieres->random();
            $typeCours = $typesCours->where('nom', $sessionData['type'])->first() ?? $typesCours->random();
            $statut = $statutsSession->where('nom', $sessionData['statut'])->first() ?? $statutsSession->random();
            $enseignant = $enseignants->random();
            $classe = $classes->random();
            $semestre = $semestres->random();

            $session = SessionDeCours::create([
                'semester_id' => $semestre->id,
                'classe_id' => $classe->id,
                'matiere_id' => $matiere->id,
                'enseignant_id' => $enseignant->id,
                'type_cours_id' => $typeCours->id,
                'status_id' => $statut->id,
                'start_time' => $sessionData['start_time'],
                'end_time' => $sessionData['end_time'],
                'location' => $sessionData['location'],
                'notes' => $sessionData['notes'],
                'annee_academique_id' => $anneeAcademique->id,
            ]);

            $this->command->info("✅ Session créée: {$matiere->nom} - {$typeCours->nom} - {$statut->nom}");
        }
    }

    private function createTestPresences()
    {
        $sessions = SessionDeCours::all();
        $etudiants = Etudiant::all();
        $statutsPresence = StatutPresence::all();

        if ($sessions->isEmpty() || $etudiants->isEmpty()) {
            $this->command->warn('Données insuffisantes pour créer des présences de test.');
            return;
        }

        $statuts = ['Présent', 'Absent', 'Justifié', 'Retard'];

        foreach ($sessions as $session) {
            $etudiantsSession = $etudiants->where('classe_id', $session->classe_id);

            if ($etudiantsSession->isEmpty()) {
                continue;
            }

            foreach ($etudiantsSession as $etudiant) {
                $statutPresence = $statutsPresence->where('nom', $statuts[array_rand($statuts)])->first()
                    ?? $statutsPresence->random();

                Presence::create([
                    'etudiant_id' => $etudiant->id,
                    'course_session_id' => $session->id,
                    'statut_presence_id' => $statutPresence->id,
                    'enregistre_le' => Carbon::now(),
                    'enregistre_par_user_id' => 1, // Admin
                ]);
            }

            $this->command->info("✅ Présences créées pour la session: {$session->matiere->nom}");
        }
    }
}
