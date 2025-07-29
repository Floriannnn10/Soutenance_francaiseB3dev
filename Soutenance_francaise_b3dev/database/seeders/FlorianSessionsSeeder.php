<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SessionDeCours;
use App\Models\Enseignant;
use App\Models\Matiere;
use App\Models\Classe;
use App\Models\TypeCours;
use App\Models\StatutSession;
use App\Models\AnneeAcademique;
use App\Models\Semestre;
use Carbon\Carbon;

class FlorianSessionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer l'enseignant Florian Banga
        $florian = Enseignant::where('email', 'florian@ifran.ci')
            ->orWhereHas('user', function($query) {
                $query->where('email', 'florian@ifran.ci');
            })->first();

        if (!$florian) {
            $this->command->error('L\'enseignant Florian Banga (florian@ifran.ci) n\'existe pas.');
            return;
        }

        $this->command->info("Création des sessions pour Florian Banga (ID: {$florian->id})...");

        // Récupérer les matières assignées à Florian
        $matieresFlorian = $florian->matieres;

        if ($matieresFlorian->isEmpty()) {
            // Si aucune matière assignée, assigner quelques matières par défaut
            $matieresParDefaut = Matiere::take(3)->get();
            $florian->matieres()->attach($matieresParDefaut->pluck('id'));
            $matieresFlorian = $matieresParDefaut;
            $this->command->info("✅ Matières assignées à Florian : " . $matieresFlorian->pluck('nom')->implode(', '));
        } else {
            $this->command->info("✅ Matières de Florian : " . $matieresFlorian->pluck('nom')->implode(', '));
        }

        // Récupérer les classes disponibles
        $classes = Classe::all();
        if ($classes->isEmpty()) {
            $this->command->error('Aucune classe disponible.');
            return;
        }

        // Récupérer l'année académique active
        $anneeAcademique = AnneeAcademique::where('actif', true)->first();
        if (!$anneeAcademique) {
            $this->command->error('Aucune année académique active trouvée.');
            return;
        }

        // Récupérer les semestres
        $semestres = Semestre::where('annee_academique_id', $anneeAcademique->id)->get();
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

        // Récupérer le statut Planifiée
        $statutPlanifie = StatutSession::where('nom', 'Planifiée')->first();
        if (!$statutPlanifie) {
            $this->command->error('Le statut Planifiée n\'existe pas.');
            return;
        }

        $sessionCount = 0;

        // Créer des sessions pour chaque classe avec les matières de Florian
        foreach ($classes as $classe) {
            $this->createSessionsForClasse($florian, $classe, $matieresFlorian, $semestres, $anneeAcademique, $typePresentiel, $statutPlanifie, $sessionCount);
        }

        $this->command->info("🎉 {$sessionCount} sessions de cours ont été créées pour Florian Banga !");
        $this->command->info('📚 Sessions créées : Type Présentiel uniquement');
        $this->command->info('📅 Sessions réparties sur les prochaines semaines');
        $this->command->info('👨‍🏫 Florian peut maintenant se connecter et marquer les présences');
    }

    private function createSessionsForClasse($florian, $classe, $matieresFlorian, $semestres, $anneeAcademique, $typePresentiel, $statutPlanifie, &$sessionCount)
    {
        $this->command->info("Création des sessions pour la classe {$classe->nom}...");

        // Créer 2 sessions par matière de Florian pour cette classe
        foreach ($matieresFlorian as $matiere) {
            for ($i = 0; $i < 2; $i++) {
                $semestre = $semestres->random();

                // Date de début : prochaines semaines
                $startTime = Carbon::now()->addWeeks($i + 1)->setTime(8 + ($i * 2), 0, 0);
                $endTime = $startTime->copy()->addHours(2);

                SessionDeCours::create([
                    'classe_id' => $classe->id,
                    'matiere_id' => $matiere->id,
                    'enseignant_id' => $florian->id,
                    'type_cours_id' => $typePresentiel->id,
                    'semester_id' => $semestre->id,
                    'annee_academique_id' => $anneeAcademique->id,
                    'status_id' => $statutPlanifie->id,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'location' => 'Salle ' . rand(1, 15),
                    'notes' => "Session de {$matiere->nom} pour {$classe->nom} - Enseignant: Florian Banga",
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $sessionCount++;
                $this->command->info("✅ Session créée: {$matiere->nom} - {$startTime->format('d/m/Y H:i')} ({$classe->nom})");
            }
        }
    }
}
