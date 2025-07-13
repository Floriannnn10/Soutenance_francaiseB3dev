<?php

namespace Database\Seeders;

use App\Models\Presence;
use App\Models\Etudiant;
use App\Models\SessionDeCours;
use App\Models\StatutPresence;
use App\Models\User;
use Illuminate\Database\Seeder;

class PresencesSeeder extends Seeder
{
    public function run(): void
    {
        $etudiants = Etudiant::all();
        $sessions = SessionDeCours::all();
        $statutPresent = StatutPresence::where('nom', StatutPresence::PRESENT)->first();
        $statutAbsent = StatutPresence::where('nom', StatutPresence::ABSENT)->first();
        $statutRetard = StatutPresence::where('nom', StatutPresence::EN_RETARD)->first();
        $user = User::where('role_id', 3)->first(); // Enseignant

        foreach ($sessions as $session) {
            foreach ($etudiants as $index => $etudiant) {
                $statut = $index % 3 == 0 ? $statutPresent : ($index % 3 == 1 ? $statutRetard : $statutAbsent);

                Presence::create([
                    'etudiant_id' => $etudiant->id,
                    'session_de_cours_id' => $session->id,
                    'statut_presence_id' => $statut->id,
                    'enregistre_par_utilisateur_id' => $user->id,
                    'est_justifiee' => false,
                    'motif_justification' => null,
                    'enregistre_a' => now(),
                ]);
            }
        }
    }
}
