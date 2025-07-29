<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Enseignant;
use App\Models\SessionDeCours;
use App\Models\Matiere;
use App\Models\Classe;
use App\Models\TypeCours;
use App\Models\StatutSession;
use App\Models\AnneeAcademique;
use App\Models\Semestre;
use App\Models\Etudiant;
use App\Models\Presence;
use App\Models\StatutPresence;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class FlorianCompleteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('=== CRÉATION COMPLÈTE POUR FLORIAN BANGA ===');

        // 1. Créer ou récupérer l'utilisateur Florian
        $user = User::where('email', 'florian@ifran.ci')->first();

        if (!$user) {
            $this->command->info('Création de l\'utilisateur Florian...');
            $user = User::create([
                'nom' => 'Banga',
                'prenom' => 'Florian',
                'email' => 'florian@ifran.ci',
                'password' => Hash::make('password'),
            ]);
            $this->command->info('✅ Utilisateur Florian créé');
        } else {
            $this->command->info('✅ Utilisateur Florian existe déjà');
        }

        // 2. Attacher le rôle enseignant
        $roleEnseignant = Role::where('code', 'enseignant')->first();
        if ($roleEnseignant && !$user->roles->contains($roleEnseignant->id)) {
            $user->roles()->attach($roleEnseignant->id);
            $this->command->info('✅ Rôle enseignant attaché');
        }

        // 3. Créer ou récupérer le profil enseignant
        $florian = Enseignant::where('user_id', $user->id)->first();

        if (!$florian) {
            $this->command->info('Création du profil enseignant Florian...');
            $florian = Enseignant::create([
                'user_id' => $user->id,
                'nom' => 'Banga',
                'prenom' => 'Florian',
            ]);
            $this->command->info('✅ Profil enseignant Florian créé');
        } else {
            $this->command->info('✅ Profil enseignant Florian existe déjà');
        }

        // 4. Assigner des matières à Florian
        $matieres = Matiere::take(3)->get();
        if ($matieres->isNotEmpty()) {
            $florian->matieres()->sync($matieres->pluck('id'));
            $this->command->info('✅ Matières assignées: ' . $matieres->pluck('nom')->implode(', '));
        }

        // 5. Récupérer les données nécessaires
        $classes = Classe::all();
        $anneeAcademique = AnneeAcademique::where('actif', true)->first();
        $semestres = Semestre::where('annee_academique_id', $anneeAcademique->id)->get();
        $typePresentiel = TypeCours::where('nom', 'Présentiel')->first();
        $statutPlanifie = StatutSession::where('nom', 'Planifiée')->first();

        if (!$classes->isEmpty() && $anneeAcademique && !$semestres->isEmpty() && $typePresentiel && $statutPlanifie) {
            $this->command->info('✅ Toutes les données nécessaires sont disponibles');
        } else {
            $this->command->error('❌ Données manquantes pour créer les sessions');
            return;
        }

        // 6. Créer les sessions pour Florian avec des dates variées
        $sessionCount = 0;
        $semaines = [1, 2, 3, 4]; // 4 semaines différentes
        $heures = [8, 10, 14, 16]; // 4 créneaux horaires
        $minutes = [0, 15, 30, 45]; // Variations de minutes

        foreach ($classes as $classe) {
            foreach ($matieres as $matiere) {
                // Créer 2 sessions par matière par classe avec des dates variées
                for ($i = 0; $i < 2; $i++) {
                    $semestre = $semestres->random();

                    // Date variée : semaines différentes
                    $semaine = $semaines[array_rand($semaines)];
                    $heure = $heures[array_rand($heures)];
                    $minute = $minutes[array_rand($minutes)];

                    // Créer une date réaliste
                    $startTime = Carbon::now()
                        ->addWeeks($semaine)
                        ->setTime($heure, $minute, 0);

                    // Éviter les weekends
                    while ($startTime->isWeekend()) {
                        $startTime->addDay();
                    }

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

        $this->command->info("✅ {$sessionCount} sessions créées pour Florian");

        // 7. Créer des présences pour les sessions
        $sessionsFlorian = SessionDeCours::where('enseignant_id', $florian->id)->get();
        $statutsPresence = StatutPresence::all();
        $presenceCount = 0;

        foreach ($sessionsFlorian as $session) {
            $etudiants = Etudiant::where('classe_id', $session->classe_id)->get();

            foreach ($etudiants as $etudiant) {
                $statut = $statutsPresence->random();

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
        }

        $this->command->info("✅ {$presenceCount} présences créées pour Florian");

        $this->command->info("\n🎉 FLORIAN BANGA EST PRÊT !");
        $this->command->info('Email: florian@ifran.ci');
        $this->command->info('Mot de passe: password');
        $this->command->info("Sessions: {$sessionCount}");
        $this->command->info("Présences: {$presenceCount}");
    }
}
