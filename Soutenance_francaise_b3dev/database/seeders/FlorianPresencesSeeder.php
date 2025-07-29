<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SessionDeCours;
use App\Models\Etudiant;
use App\Models\Presence;
use App\Models\StatutPresence;
use App\Models\Enseignant;
use Carbon\Carbon;

class FlorianPresencesSeeder extends Seeder
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

        // Récupérer les sessions de cours de Florian
        $sessionsFlorian = SessionDeCours::where('enseignant_id', $florian->id)->get();

        if ($sessionsFlorian->isEmpty()) {
            $this->command->error('Aucune session de cours trouvée pour Florian Banga.');
            return;
        }

        // Récupérer les statuts de présence
        $statutsPresence = StatutPresence::all();
        if ($statutsPresence->isEmpty()) {
            $this->command->error('Aucun statut de présence trouvé.');
            return;
        }

        $presenceCount = 0;

        $this->command->info("Création des présences pour les sessions de Florian Banga...");

        foreach ($sessionsFlorian as $session) {
            // Récupérer les étudiants de la classe de cette session
            $etudiants = Etudiant::where('classe_id', $session->classe_id)->get();

            if ($etudiants->isEmpty()) {
                $this->command->warn("Aucun étudiant trouvé pour la classe {$session->classe->nom}.");
                continue;
            }

            $this->command->info("  📝 Session: {$session->matiere->nom} - {$session->start_time->format('d/m/Y H:i')} ({$session->classe->nom})");

            foreach ($etudiants as $etudiant) {
                // Répartition aléatoire des statuts de présence
                $statut = $statutsPresence->random();

                // Créer la présence
                Presence::create([
                    'etudiant_id' => $etudiant->id,
                    'course_session_id' => $session->id,
                    'statut_presence_id' => $statut->id,
                    'enregistre_le' => $session->start_time->copy()->addMinutes(rand(0, 30)),
                    'enregistre_par_user_id' => $florian->user_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $presenceCount++;
            }

            $this->command->info("    ✅ {$etudiants->count()} présences créées pour cette session");
        }

        $this->command->info("🎉 {$presenceCount} présences ont été créées pour les sessions de Florian Banga !");
        $this->command->info('📊 Répartition : Présent, Absent, Retard, Justifié');
        $this->command->info('👥 Tous les étudiants ont des présences enregistrées');
        $this->command->info('👨‍🏫 Florian peut maintenant voir et modifier ces présences');
    }
}
