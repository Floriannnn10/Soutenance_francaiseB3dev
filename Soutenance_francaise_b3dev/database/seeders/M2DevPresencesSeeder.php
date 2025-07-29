<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SessionDeCours;
use App\Models\Etudiant;
use App\Models\Presence;
use App\Models\StatutPresence;
use App\Models\Classe;
use Carbon\Carbon;

class M2DevPresencesSeeder extends Seeder
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

        // Récupérer les sessions de cours pour ces classes
        $sessionsM2DevA = SessionDeCours::where('classe_id', $classeM2DevA->id)->get();
        $sessionsM2DevB = SessionDeCours::where('classe_id', $classeM2DevB->id)->get();

        if ($sessionsM2DevA->isEmpty() && $sessionsM2DevB->isEmpty()) {
            $this->command->error('Aucune session de cours trouvée pour les classes M2 DEV.');
            return;
        }

        // Récupérer les statuts de présence
        $statutsPresence = StatutPresence::all();
        if ($statutsPresence->isEmpty()) {
            $this->command->error('Aucun statut de présence trouvé.');
            return;
        }

        $presenceCount = 0;

        // Créer des présences pour M2 DEV A
        $this->createPresencesForClasse($sessionsM2DevA, $classeM2DevA, $statutsPresence, $presenceCount, 'M2 DEV A');

        // Créer des présences pour M2 DEV B
        $this->createPresencesForClasse($sessionsM2DevB, $classeM2DevB, $statutsPresence, $presenceCount, 'M2 DEV B');

        $this->command->info("🎉 {$presenceCount} présences ont été créées pour les sessions M2 DEV !");
        $this->command->info('📊 Répartition : Présent, Absent, Retard, Justifié');
        $this->command->info('👥 Tous les étudiants ont des présences enregistrées');
    }

    private function createPresencesForClasse($sessions, $classe, $statutsPresence, &$presenceCount, $classeNom)
    {
        $this->command->info("Création des présences pour {$classeNom}...");

        // Récupérer les étudiants de cette classe
        $etudiants = Etudiant::where('classe_id', $classe->id)->get();

        if ($etudiants->isEmpty()) {
            $this->command->error("Aucun étudiant trouvé pour {$classeNom}.");
            return;
        }

        foreach ($sessions as $session) {
            $this->command->info("  📝 Session: {$session->matiere->nom} - {$session->start_time->format('d/m/Y H:i')}");

            foreach ($etudiants as $etudiant) {
                // Répartition aléatoire des statuts de présence
                $statut = $statutsPresence->random();

                // Créer la présence
                Presence::create([
                    'etudiant_id' => $etudiant->id,
                    'course_session_id' => $session->id,
                    'statut_presence_id' => $statut->id,
                    'enregistre_le' => $session->start_time->copy()->addMinutes(rand(0, 30)),
                    'enregistre_par_user_id' => 1, // Admin
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $presenceCount++;
            }

            $this->command->info("    ✅ {$etudiants->count()} présences créées pour cette session");
        }
    }
}
