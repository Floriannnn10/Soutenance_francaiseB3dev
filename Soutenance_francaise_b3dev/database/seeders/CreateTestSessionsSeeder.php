<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SessionDeCours;
use App\Models\Matiere;
use App\Models\Classe;
use App\Models\Enseignant;
use App\Models\TypeCours;
use App\Models\StatutSession;
use App\Models\Semestre;
use App\Models\AnneeAcademique;
use Carbon\Carbon;

class CreateTestSessionsSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('🎯 Création des sessions de test pour l\'agenda...');

        // Récupérer les données nécessaires
        $matieres = Matiere::all();
        $classes = Classe::all();
        $enseignants = Enseignant::all();
        $typePresentiel = TypeCours::where('nom', 'Présentiel')->first();
        $statutProgrammee = StatutSession::where('nom', 'Programmée')->first();
        $semestre = Semestre::where('actif', true)->first();
        $anneeAcademique = AnneeAcademique::where('actif', true)->first();

        if (!$matieres->count() || !$classes->count() || !$enseignants->count()) {
            $this->command->error('❌ Données manquantes. Exécutez d\'abord les autres seeders.');
            return;
        }

        // Créer des sessions passées (récentes)
        $this->command->info('📅 Création des sessions récentes (passées)...');
        for ($i = 1; $i <= 5; $i++) {
            $date = Carbon::now()->subDays($i);
            $session = SessionDeCours::create([
                'matiere_id' => $matieres->random()->id,
                'classe_id' => $classes->random()->id,
                'enseignant_id' => $enseignants->random()->id,
                'type_cours_id' => $typePresentiel->id,
                'status_id' => $statutProgrammee->id,
                'semester_id' => $semestre->id,
                'annee_academique_id' => $anneeAcademique->id,
                'start_time' => $date->copy()->setTime(8 + $i, 0, 0),
                'end_time' => $date->copy()->setTime(10 + $i, 0, 0),
                'location' => 'Salle ' . ($i + 1),
                'notes' => 'Session de test passée ' . $i,
            ]);
            $this->command->info("✅ Session passée créée: {$session->matiere->nom} - {$date->format('d/m/Y H:i')}");
        }

        // Créer des sessions futures
        $this->command->info('🚀 Création des sessions futures...');
        for ($i = 1; $i <= 8; $i++) {
            $date = Carbon::now()->addDays($i);
            $session = SessionDeCours::create([
                'matiere_id' => $matieres->random()->id,
                'classe_id' => $classes->random()->id,
                'enseignant_id' => $enseignants->random()->id,
                'type_cours_id' => $typePresentiel->id,
                'status_id' => $statutProgrammee->id,
                'semester_id' => $semestre->id,
                'annee_academique_id' => $anneeAcademique->id,
                'start_time' => $date->copy()->setTime(9 + ($i % 3), 0, 0),
                'end_time' => $date->copy()->setTime(11 + ($i % 3), 0, 0),
                'location' => 'Salle ' . ($i + 10),
                'notes' => 'Session de test future ' . $i,
            ]);
            $this->command->info("✅ Session future créée: {$session->matiere->nom} - {$date->format('d/m/Y H:i')}");
        }

        // Créer des sessions pour aujourd'hui
        $this->command->info('📅 Création des sessions pour aujourd\'hui...');
        $aujourdhui = Carbon::today();
        for ($i = 0; $i < 3; $i++) {
            $heure = 14 + $i; // 14h, 15h, 16h
            $session = SessionDeCours::create([
                'matiere_id' => $matieres->random()->id,
                'classe_id' => $classes->random()->id,
                'enseignant_id' => $enseignants->random()->id,
                'type_cours_id' => $typePresentiel->id,
                'status_id' => $statutProgrammee->id,
                'semester_id' => $semestre->id,
                'annee_academique_id' => $anneeAcademique->id,
                'start_time' => $aujourdhui->copy()->setTime($heure, 0, 0),
                'end_time' => $aujourdhui->copy()->setTime($heure + 2, 0, 0),
                'location' => 'Salle ' . ($i + 20),
                'notes' => 'Session aujourd\'hui ' . ($i + 1),
            ]);
            $this->command->info("✅ Session aujourd'hui créée: {$session->matiere->nom} - {$heure}h00");
        }

        $this->command->info('🎉 Sessions de test créées avec succès !');
        $this->command->info('📊 Statistiques:');
        $this->command->info('   - Sessions passées: ' . SessionDeCours::where('start_time', '<', now())->count());
        $this->command->info('   - Sessions futures: ' . SessionDeCours::where('start_time', '>', now())->count());
        $this->command->info('   - Total: ' . SessionDeCours::count());
    }
}
