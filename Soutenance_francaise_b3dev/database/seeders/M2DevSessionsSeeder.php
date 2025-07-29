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

class M2DevSessionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer les classes M2 DEV
        $classeM2DevA = Classe::where('nom', 'M2 DEV A')->first();
        $classeM2DevB = Classe::where('nom', 'M2 DEV B')->first();

        if (!$classeM2DevA || !$classeM2DevB) {
            $this->command->error('Les classes M2 DEV A et M2 DEV B n\'existent pas.');
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

        // Récupérer les types de cours
        $typePresentiel = TypeCours::where('nom', 'Présentiel')->first();
        $typeWorkshop = TypeCours::where('nom', 'Workshop')->first();
        $typeElearning = TypeCours::where('nom', 'E-learning')->first();

        if (!$typePresentiel || !$typeWorkshop || !$typeElearning) {
            $this->command->error('Les types de cours nécessaires n\'existent pas.');
            return;
        }

        $statutPlanifie = StatutSession::where('nom', 'Planifiée')->first();
        $statutEnCours = StatutSession::where('nom', 'En cours')->first();

        $sessionCount = 0;

        // Créer des sessions pour M2 DEV A
        $this->createSessionsForClasse($classeM2DevA, $matieres, $enseignants, $semestres, $anneeAcademique, $typePresentiel, $typeWorkshop, $typeElearning, $statutPlanifie, $statutEnCours, $sessionCount, 'M2 DEV A');

        // Créer des sessions pour M2 DEV B
        $this->createSessionsForClasse($classeM2DevB, $matieres, $enseignants, $semestres, $anneeAcademique, $typePresentiel, $typeWorkshop, $typeElearning, $statutPlanifie, $statutEnCours, $sessionCount, 'M2 DEV B');

        $this->command->info("🎉 {$sessionCount} sessions de cours ont été créées pour les classes M2 DEV !");
        $this->command->info('📚 Sessions créées : Présentiel, Workshop, E-learning');
        $this->command->info('📅 Sessions réparties sur les prochaines semaines');
    }

    private function createSessionsForClasse($classe, $matieres, $enseignants, $semestres, $anneeAcademique, $typePresentiel, $typeWorkshop, $typeElearning, $statutPlanifie, $statutEnCours, &$sessionCount, $classeNom)
    {
        $this->command->info("Création des sessions pour {$classeNom}...");

        // Sessions en présentiel
        for ($i = 0; $i < 3; $i++) {
            $matiere = $matieres->random();
            $enseignant = $enseignants->random();
            $semestre = $semestres->random();

            // Date de début : prochaines semaines
            $startTime = Carbon::now()->addWeeks($i + 1)->setTime(9, 0, 0);
            $endTime = $startTime->copy()->addHours(2);

            SessionDeCours::create([
                'classe_id' => $classe->id,
                'matiere_id' => $matiere->id,
                'enseignant_id' => $enseignant->id,
                'type_cours_id' => $typePresentiel->id,
                'semester_id' => $semestre->id,
                'annee_academique_id' => $anneeAcademique->id,
                'status_id' => $statutPlanifie->id,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'location' => 'Salle ' . rand(1, 10),
                'notes' => "Session de {$matiere->nom} pour {$classeNom}",
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $sessionCount++;
            $this->command->info("✅ Session créée: {$matiere->nom} - {$startTime->format('d/m/Y H:i')} ({$classeNom})");
        }

        // Sessions Workshop
        for ($i = 0; $i < 2; $i++) {
            $matiere = $matieres->random();
            $enseignant = $enseignants->random();
            $semestre = $semestres->random();

            $startTime = Carbon::now()->addWeeks($i + 2)->setTime(14, 0, 0);
            $endTime = $startTime->copy()->addHours(3);

            SessionDeCours::create([
                'classe_id' => $classe->id,
                'matiere_id' => $matiere->id,
                'enseignant_id' => $enseignant->id,
                'type_cours_id' => $typeWorkshop->id,
                'semester_id' => $semestre->id,
                'annee_academique_id' => $anneeAcademique->id,
                'status_id' => $statutPlanifie->id,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'location' => 'Atelier ' . rand(1, 5),
                'notes' => "Workshop {$matiere->nom} pour {$classeNom}",
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $sessionCount++;
            $this->command->info("✅ Workshop créé: {$matiere->nom} - {$startTime->format('d/m/Y H:i')} ({$classeNom})");
        }

        // Sessions E-learning
        for ($i = 0; $i < 2; $i++) {
            $matiere = $matieres->random();
            $enseignant = $enseignants->random();
            $semestre = $semestres->random();

            $startTime = Carbon::now()->addWeeks($i + 3)->setTime(10, 0, 0);
            $endTime = $startTime->copy()->addHours(1, 30);

            SessionDeCours::create([
                'classe_id' => $classe->id,
                'matiere_id' => $matiere->id,
                'enseignant_id' => $enseignant->id,
                'type_cours_id' => $typeElearning->id,
                'semester_id' => $semestre->id,
                'annee_academique_id' => $anneeAcademique->id,
                'status_id' => $statutPlanifie->id,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'location' => 'Plateforme en ligne',
                'notes' => "E-learning {$matiere->nom} pour {$classeNom}",
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $sessionCount++;
            $this->command->info("✅ E-learning créé: {$matiere->nom} - {$startTime->format('d/m/Y H:i')} ({$classeNom})");
        }
    }
}
