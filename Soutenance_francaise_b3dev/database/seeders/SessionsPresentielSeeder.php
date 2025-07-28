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

class SessionsPresentielSeeder extends Seeder
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

        // Récupérer le type Présentiel
        $typePresentiel = TypeCours::where('nom', 'Présentiel')->first();

        if (!$typePresentiel) {
            $this->command->error('Le type Présentiel n\'existe pas.');
            return;
        }

        $statutPlanifie = StatutSession::where('nom', 'Planifiée')->first();

        $sessionCount = 0;

        foreach ($classes as $classe) {
            // Créer 4 sessions en présentiel pour chaque classe
            for ($i = 0; $i < 4; $i++) {
                $matiere = $matieres->random();
                $enseignant = $enseignants->random();
                $semestre = $semestres->random();

                // Créer des dates dans le futur pour les sessions
                $dateSession = Carbon::now()->addDays(rand(1, 30))->setTime(rand(8, 16), 0, 0);

                SessionDeCours::create([
                    'classe_id' => $classe->id,
                    'matiere_id' => $matiere->id,
                    'enseignant_id' => $enseignant->id,
                    'type_cours_id' => $typePresentiel->id,
                    'status_id' => $statutPlanifie->id,
                    'start_time' => $dateSession,
                    'end_time' => $dateSession->copy()->addHours(2),
                    'location' => 'Salle ' . rand(1, 20),
                    'notes' => 'Session en présentiel - ' . $matiere->nom,
                    'annee_academique_id' => $anneeAcademique->id,
                    'semester_id' => $semestre->id,
                ]);

                $sessionCount++;
            }
        }

        $this->command->info("{$sessionCount} sessions en présentiel créées avec succès pour les classes M2 DEV !");
    }
}
