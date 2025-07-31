<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Etudiant;
use App\Models\Presence;
use App\Models\SessionDeCours;
use App\Models\StatutPresence;
use App\Models\Classe;
use App\Models\Matiere;
use App\Models\AnneeAcademique;
use App\Models\Semestre;
use App\Models\Enseignant;
use App\Models\TypeCours;
use App\Models\StatutSession;
use App\Models\User;
use App\Models\Role;
use Carbon\Carbon;

class CreateDroppedStudentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🎯 Création d\'étudiants en situation de dropping...');

        // Récupérer les données nécessaires
        $anneeActive = AnneeAcademique::getActive();
        $semestreActif = Semestre::where('actif', true)->first();
        $classe = Classe::first();
        $matiere = Matiere::first();
        $enseignant = Enseignant::first();
        $typeCours = TypeCours::first();
        $statutSession = StatutSession::first();
        $statutPresent = StatutPresence::where('nom', 'Présent')->first();
        $statutAbsent = StatutPresence::where('nom', 'Absent')->first();

        if (!$anneeActive || !$semestreActif || !$classe || !$matiere || !$enseignant) {
            $this->command->error('❌ Données insuffisantes. Assurez-vous d\'avoir des données de base.');
            return;
        }

        // Créer des étudiants en situation de dropping
        $timestamp = time();
        $etudiantsDropped = [
            [
                'prenom' => 'Jean',
                'nom' => 'Dupont',
                'email' => "jean.dupont.dropped.{$timestamp}@test.com",
                'taux_presence' => 25, // 25% - sera droppé
                'description' => 'Étudiant avec 25% de présence - SITUATION DE DROPPING'
            ],
            [
                'prenom' => 'Marie',
                'nom' => 'Martin',
                'email' => "marie.martin.dropped.{$timestamp}@test.com",
                'taux_presence' => 20, // 20% - sera droppé
                'description' => 'Étudiant avec 20% de présence - SITUATION DE DROPPING'
            ],
            [
                'prenom' => 'Pierre',
                'nom' => 'Bernard',
                'email' => "pierre.bernard.dropped.{$timestamp}@test.com",
                'taux_presence' => 30, // 30% - sera droppé
                'description' => 'Étudiant avec 30% de présence - SITUATION DE DROPPING'
            ],
            [
                'prenom' => 'Sophie',
                'nom' => 'Petit',
                'email' => "sophie.petit.dropped.{$timestamp}@test.com",
                'taux_presence' => 15, // 15% - sera droppé
                'description' => 'Étudiant avec 15% de présence - SITUATION DE DROPPING'
            ],
            [
                'prenom' => 'Lucas',
                'nom' => 'Moreau',
                'email' => "lucas.moreau.dropped.{$timestamp}@test.com",
                'taux_presence' => 28, // 28% - sera droppé
                'description' => 'Étudiant avec 28% de présence - SITUATION DE DROPPING'
            ]
        ];

        $count = 0;

        foreach ($etudiantsDropped as $etudiantData) {
            // Créer l'utilisateur d'abord
            $user = User::create([
                'nom' => $etudiantData['nom'],
                'prenom' => $etudiantData['prenom'],
                'email' => $etudiantData['email'],
                'password' => bcrypt('password'),
            ]);

            // Attacher le rôle étudiant
            $roleEtudiant = Role::where('code', 'etudiant')->first();
            if ($roleEtudiant) {
                $user->roles()->attach($roleEtudiant->id);
            }

            // Créer l'étudiant
            $etudiant = Etudiant::create([
                'user_id' => $user->id,
                'classe_id' => $classe->id,
                'prenom' => $etudiantData['prenom'],
                'nom' => $etudiantData['nom'],
                'email' => $etudiantData['email'],
                'date_naissance' => Carbon::now()->subYears(20),
            ]);

            // Créer 10 sessions de cours pour cet étudiant
            for ($i = 1; $i <= 10; $i++) {
                $session = SessionDeCours::create([
                    'classe_id' => $classe->id,
                    'matiere_id' => $matiere->id,
                    'enseignant_id' => $enseignant->id,
                    'type_cours_id' => $typeCours->id,
                    'status_id' => $statutSession->id,
                    'annee_academique_id' => $anneeActive->id,
                    'semester_id' => $semestreActif->id,
                    'start_time' => Carbon::now()->subDays(30 - $i)->setTime(9, 0),
                    'end_time' => Carbon::now()->subDays(30 - $i)->setTime(11, 0),
                ]);

                // Créer la présence selon le taux souhaité
                $nombrePresences = round(($etudiantData['taux_presence'] / 100) * 10);

                if ($i <= $nombrePresences) {
                    // Présent
                    Presence::create([
                        'etudiant_id' => $etudiant->id,
                        'course_session_id' => $session->id,
                        'statut_presence_id' => $statutPresent->id,
                        'enregistre_le' => $session->start_time,
                        'enregistre_par_user_id' => $user->id, // Utilisateur créé
                    ]);
                } else {
                    // Absent
                    Presence::create([
                        'etudiant_id' => $etudiant->id,
                        'course_session_id' => $session->id,
                        'statut_presence_id' => $statutAbsent->id,
                        'enregistre_le' => $session->start_time,
                        'enregistre_par_user_id' => $user->id, // Utilisateur créé
                    ]);
                }
            }

            $count++;
            $this->command->info("✅ {$etudiantData['prenom']} {$etudiantData['nom']} créé avec {$etudiantData['taux_presence']}% de présence - {$etudiantData['description']}");
            $this->command->info("   📧 Email: {$etudiantData['email']}");
            $this->command->info("   🆔 ID: {$etudiant->id}");
            $this->command->info("   👤 User ID: {$user->id}");
        }

        $this->command->info("\n🎉 {$count} étudiants en situation de dropping ont été créés !");
        $this->command->info("\n📋 Identifiants des étudiants créés :");

        foreach ($etudiantsDropped as $index => $etudiantData) {
            $this->command->info("   " . ($index + 1) . ". {$etudiantData['prenom']} {$etudiantData['nom']} - Email: {$etudiantData['email']} - Taux: {$etudiantData['taux_presence']}%");
        }

        $this->command->info("\n🔧 Pour tester le système de drops automatiques :");
        $this->command->info("   php artisan drops:process-automatic");
        $this->command->info("\n🌐 Pour voir les notifications :");
        $this->command->info("   Accédez à /test-drops dans votre navigateur");
    }
}
