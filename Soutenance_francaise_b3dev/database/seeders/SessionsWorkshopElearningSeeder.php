<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SessionDeCours;
use App\Models\Classe;
use App\Models\Matiere;
use App\Models\Enseignant;
use App\Models\TypeCours;
use App\Models\StatutSession;
use App\Models\AnneeAcademique;
use App\Models\Semestre;
use Carbon\Carbon;

class SessionsWorkshopElearningSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer les classes M2 DEV
        $classes = Classe::whereIn('nom', ['M2 DEV A', 'M2 DEV B'])->get();

        if ($classes->isEmpty()) {
            $this->command->error('Les classes M2 DEV n\'existent pas.');
            return;
        }

        $matieres = Matiere::all();
        $enseignants = Enseignant::all();
        $anneeAcademique = AnneeAcademique::where('actif', true)->first();
        $semestres = Semestre::where('annee_academique_id', $anneeAcademique->id)->get();

        if (!$anneeAcademique) {
            $this->command->error('Aucune année académique active trouvée.');
            return;
        }

        if ($semestres->isEmpty()) {
            $this->command->error('Aucun semestre trouvé pour cette année académique.');
            return;
        }

        // Récupérer les types Workshop et E-learning
        $typeWorkshop = TypeCours::where('nom', 'Workshop')->first();
        $typeElearning = TypeCours::where('nom', 'E-learning')->first();

        if (!$typeWorkshop || !$typeElearning) {
            $this->command->error('Les types Workshop et E-learning n\'existent pas.');
            return;
        }

        $statutPlanifie = StatutSession::where('nom', 'Planifiée')->first();
        $statutEnCours = StatutSession::where('nom', 'En cours')->first();

        $sessionCount = 0;

        foreach ($classes as $classe) {
            // Créer 3 sessions Workshop
            for ($i = 0; $i < 3; $i++) {
                $matiere = $matieres->random();
                $enseignant = $enseignants->random();
                $semestre = $semestres->random();

                // Créer des dates dans le futur pour les sessions
                $dateSession = Carbon::now()->addDays(rand(1, 30))->setTime(rand(8, 16), 0, 0);

                SessionDeCours::create([
                    'classe_id' => $classe->id,
                    'matiere_id' => $matiere->id,
                    'enseignant_id' => $enseignant->id,
                    'type_cours_id' => $typeWorkshop->id,
                    'status_id' => $statutPlanifie->id,
                    'start_time' => $dateSession,
                    'end_time' => $dateSession->copy()->addHours(2),
                    'location' => 'Salle Workshop ' . rand(1, 5),
                    'notes' => 'Session Workshop - ' . $matiere->nom,
                    'annee_academique_id' => $anneeAcademique->id,
                    'semester_id' => $semestre->id,
                ]);

                $sessionCount++;
            }

            // Créer 2 sessions E-learning
            for ($i = 0; $i < 2; $i++) {
                $matiere = $matieres->random();
                $enseignant = $enseignants->random();
                $semestre = $semestres->random();

                // Créer des dates dans le futur pour les sessions
                $dateSession = Carbon::now()->addDays(rand(1, 30))->setTime(rand(8, 16), 0, 0);

                SessionDeCours::create([
                    'classe_id' => $classe->id,
                    'matiere_id' => $matiere->id,
                    'enseignant_id' => $enseignant->id,
                    'type_cours_id' => $typeElearning->id,
                    'status_id' => $statutPlanifie->id,
                    'start_time' => $dateSession,
                    'end_time' => $dateSession->copy()->addHours(1.5),
                    'location' => 'Plateforme E-learning',
                    'notes' => 'Session E-learning - ' . $matiere->nom,
                    'annee_academique_id' => $anneeAcademique->id,
                    'semester_id' => $semestre->id,
                ]);

                $sessionCount++;
            }
        }

        $this->command->info("{$sessionCount} sessions Workshop et E-learning créées avec succès pour les classes M2 DEV !");
    }
}
