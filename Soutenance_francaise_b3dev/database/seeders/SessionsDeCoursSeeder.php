<?php

namespace Database\Seeders;

use App\Models\SessionDeCours;
use App\Models\Semestre;
use App\Models\Classe;
use App\Models\Matiere;
use App\Models\Enseignant;
use App\Models\TypeCours;
use App\Models\StatutSession;
use Illuminate\Database\Seeder;

class SessionsDeCoursSeeder extends Seeder
{
    public function run(): void
    {
        $semestre = Semestre::first();
        $classe = Classe::first();
        $matiere = Matiere::first();
        $enseignant = Enseignant::first();
        $typeCours = TypeCours::where('nom', TypeCours::CM)->first();
        $statutSession = StatutSession::where('nom', StatutSession::PREVUE)->first();

        $sessions = [
            [
                'date' => now()->addDays(1),
                'heure_debut' => '08:00:00',
                'heure_fin' => '10:00:00',
                'salle' => 'Amphithéâtre A',
                'commentaire' => 'Cours magistral d\'introduction',
            ],
            [
                'date' => now()->addDays(2),
                'heure_debut' => '14:00:00',
                'heure_fin' => '16:00:00',
                'salle' => 'Salle TD 101',
                'commentaire' => 'Travaux dirigés',
            ],
            [
                'date' => now()->addDays(3),
                'heure_debut' => '10:00:00',
                'heure_fin' => '12:00:00',
                'salle' => 'Salle TP 201',
                'commentaire' => 'Travaux pratiques',
            ],
        ];

        foreach ($sessions as $session) {
            SessionDeCours::create([
                'semestre_id' => $semestre->id,
                'classe_id' => $classe->id,
                'matiere_id' => $matiere->id,
                'enseignant_id' => $enseignant->id,
                'type_cours_id' => $typeCours->id,
                'statut_session_id' => $statutSession->id,
                'date' => $session['date'],
                'heure_debut' => $session['heure_debut'],
                'heure_fin' => $session['heure_fin'],
                'salle' => $session['salle'],
                'commentaire' => $session['commentaire'],
            ]);
        }
    }
}
