<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Presence;
use App\Models\Etudiant;
use App\Models\SessionDeCours;
use App\Models\StatutPresence;
use App\Models\User;
use App\Models\AnneeAcademique;
use App\Models\Semestre;

class PresencesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $etudiants = Etudiant::all();
        $sessions = SessionDeCours::all();
        $statutsPresence = StatutPresence::all();
        $users = User::all();
        $anneeAcademique = AnneeAcademique::where('actif', true)->first();
        $semestres = Semestre::where('academic_year_id', $anneeAcademique->id)->get();

        // Créer des présences pour chaque session
        foreach ($sessions as $session) {
            // Récupérer les étudiants de la classe de cette session
            $etudiantsClasse = $etudiants->where('classe_id', $session->classe_id);

            foreach ($etudiantsClasse as $etudiant) {
                $statut = $statutsPresence->random();
                $user = $users->random();
                $semestre = $semestres->random();

                Presence::create([
                    'etudiant_id' => $etudiant->id,
                    'course_session_id' => $session->id,
                    'statut_presence_id' => $statut->id,
                    'enregistre_par_user_id' => $user->id,
                    'enregistre_le' => now(),
                    'academic_year_id' => $anneeAcademique->id,
                    'semester_id' => $semestre->id,
                ]);
            }
        }
    }
}
