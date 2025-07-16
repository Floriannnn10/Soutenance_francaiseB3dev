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

class SessionsDeCoursSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classes = Classe::all();
        $matieres = Matiere::all();
        $enseignants = Enseignant::all();
        $typesCours = TypeCours::all();
        $statutsSession = StatutSession::all();
        $anneeAcademique = AnneeAcademique::where('actif', true)->first();
        $semestres = Semestre::where('academic_year_id', $anneeAcademique->id)->get();

        // Créer des sessions de cours pour chaque classe
        foreach ($classes as $classe) {
            for ($i = 0; $i < 5; $i++) {
                $matiere = $matieres->random();
                $enseignant = $enseignants->random();
                $typeCours = $typesCours->random();
                $statut = $statutsSession->random();
                $semestre = $semestres->random();

                // Générer des dates aléatoires dans le semestre
                $dateDebut = $semestre->date_debut;
                $dateFin = $semestre->date_fin;
                $dateSession = fake()->dateTimeBetween($dateDebut, $dateFin);

                SessionDeCours::create([
                    'classe_id' => $classe->id,
                    'matiere_id' => $matiere->id,
                    'enseignant_id' => $enseignant->id,
                    'type_cours_id' => $typeCours->id,
                    'status_id' => $statut->id,
                    'start_time' => $dateSession,
                    'end_time' => $dateSession->modify('+2 hours'),
                    'location' => 'Salle ' . fake()->numberBetween(1, 20),
                    'notes' => fake()->optional()->sentence(),
                    'academic_year_id' => $anneeAcademique->id,
                    'semester_id' => $semestre->id,
                ]);
            }
        }
    }
}
