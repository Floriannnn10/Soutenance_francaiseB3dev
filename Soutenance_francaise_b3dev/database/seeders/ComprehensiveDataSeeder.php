php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class ComprehensiveDataSeeder extends Seeder
{
    public function run(): void
    {
        echo "=== Création de données complètes pour le projet ===\n";

        // 1. Créer les années académiques
        $this->createAnneesAcademiques();

        // 2. Créer les promotions
        $this->createPromotions();

        // 3. Créer les classes
        $this->createClasses();

        // 4. Créer les matières
        $this->createMatieres();

        // 5. Créer les utilisateurs et profils
        $this->createUsersAndProfiles();

        // 6. Créer les sessions de cours
        $this->createSessionsDeCours();

        // 7. Créer les présences
        $this->createPresences();

        // 8. Créer les associations enseignant-matière
        $this->createEnseignantMatiereAssociations();

        echo "\n=== Données complètes créées avec succès ===\n";
    }

    private function createAnneesAcademiques()
    {
        echo "📚 Création des années académiques...\n";

        $annees = [
            ['nom' => '2023-2024', 'date_debut' => '2023-09-01', 'date_fin' => '2024-08-31'],
            ['nom' => '2024-2025', 'date_debut' => '2024-09-01', 'date_fin' => '2025-08-31'],
        ];

        foreach ($annees as $annee) {
            $existing = DB::table('annees_academiques')->where('nom', $annee['nom'])->first();
            if (!$existing) {
                DB::table('annees_academiques')->insert([
                    'nom' => $annee['nom'],
                    'date_debut' => $annee['date_debut'],
                    'date_fin' => $annee['date_fin'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                echo "✅ Année créée: {$annee['nom']}\n";
            }
        }
    }

    private function createPromotions()
    {
        echo "🎓 Création des promotions...\n";

        $promotions = [
            ['nom' => 'B3 DEV', 'description' => 'Bac+3 Développement'],
            ['nom' => 'B3 CYBER', 'description' => 'Bac+3 Cybersécurité'],
            ['nom' => 'M1 DEV', 'description' => 'Master 1 Développement'],
            ['nom' => 'M1 CYBER', 'description' => 'Master 1 Cybersécurité'],
        ];

        foreach ($promotions as $promotion) {
            $existing = DB::table('promotions')->where('nom', $promotion['nom'])->first();
            if (!$existing) {
                DB::table('promotions')->insert([
                    'nom' => $promotion['nom'],
                    'description' => $promotion['description'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                echo "✅ Promotion créée: {$promotion['nom']}\n";
            }
        }
    }

    private function createClasses()
    {
        echo "🏫 Création des classes...\n";

        $promotions = DB::table('promotions')->get();
        $classes = [];

        foreach ($promotions as $promotion) {
            $classes[] = [
                'nom' => $promotion->nom . ' A',
                'promotion_id' => $promotion->id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            $classes[] = [
                'nom' => $promotion->nom . ' B',
                'promotion_id' => $promotion->id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        foreach ($classes as $classe) {
            $existing = DB::table('classes')->where('nom', $classe['nom'])->first();
            if (!$existing) {
                DB::table('classes')->insert($classe);
                echo "✅ Classe créée: {$classe['nom']}\n";
            }
        }
    }

    private function createMatieres()
    {
        echo "📖 Création des matières...\n";

        $matieres = [
            ['nom' => 'Développement Web PHP', 'code' => 'WEB_PHP', 'coefficient' => 2.00, 'volume_horaire' => 60],
            ['nom' => 'JavaScript et Frameworks', 'code' => 'JS_FRAMEWORKS', 'coefficient' => 2.00, 'volume_horaire' => 60],
            ['nom' => 'Programmation Java', 'code' => 'JAVA', 'coefficient' => 2.50, 'volume_horaire' => 75],
            ['nom' => 'Python pour le Data Science', 'code' => 'PYTHON_DS', 'coefficient' => 2.00, 'volume_horaire' => 60],
            ['nom' => 'Sécurité des applications', 'code' => 'SECURITY', 'coefficient' => 1.50, 'volume_horaire' => 45],
            ['nom' => 'DevOps et CI/CD', 'code' => 'DEVOPS', 'coefficient' => 1.50, 'volume_horaire' => 45],
            ['nom' => 'Bases de données', 'code' => 'DATABASES', 'coefficient' => 2.00, 'volume_horaire' => 60],
            ['nom' => 'Architecture logicielle', 'code' => 'ARCHITECTURE', 'coefficient' => 1.50, 'volume_horaire' => 45],
            ['nom' => 'Intelligence artificielle', 'code' => 'AI', 'coefficient' => 2.50, 'volume_horaire' => 75],
            ['nom' => 'Cloud Computing', 'code' => 'CLOUD', 'coefficient' => 1.50, 'volume_horaire' => 45],
        ];

        foreach ($matieres as $matiere) {
            $existing = DB::table('matieres')->where('nom', $matiere['nom'])->first();
            if (!$existing) {
                DB::table('matieres')->insert([
                    'nom' => $matiere['nom'],
                    'code' => $matiere['code'],
                    'coefficient' => $matiere['coefficient'],
                    'volume_horaire' => $matiere['volume_horaire'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                echo "✅ Matière créée: {$matiere['nom']}\n";
            }
        }
    }

    private function createUsersAndProfiles()
    {
        echo "👥 Création des utilisateurs et profils...\n";

        // Créer les rôles s'ils n'existent pas
        $roles = ['admin', 'enseignant', 'etudiant', 'parent', 'coordinateur'];
        foreach ($roles as $roleCode) {
            $role = DB::table('roles')->where('code', $roleCode)->first();
            if (!$role) {
                DB::table('roles')->insert([
                    'nom' => ucfirst($roleCode),
                    'code' => $roleCode,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Créer l'admin
        $adminUser = $this->createUser('Admin', 'Système', 'admin@ifran.ci', 'password', 'admin');
        echo "✅ Admin créé: admin@ifran.ci\n";

        // Créer les enseignants
        $enseignants = [
            ['nom' => 'Banga', 'prenom' => 'Florian', 'email' => 'florian@ifran.ci'],
            ['nom' => 'Dupont', 'prenom' => 'Jean', 'email' => 'jean.dupont@ifran.ci'],
            ['nom' => 'Martin', 'prenom' => 'Marie', 'email' => 'marie.martin@ifran.ci'],
            ['nom' => 'Bernard', 'prenom' => 'Pierre', 'email' => 'pierre.bernard@ifran.ci'],
            ['nom' => 'Dubois', 'prenom' => 'Sophie', 'email' => 'sophie.dubois@ifran.ci'],
            ['nom' => 'Moreau', 'prenom' => 'Claire', 'email' => 'claire.moreau@ifran.ci'],
        ];

        foreach ($enseignants as $enseignant) {
            $user = $this->createUser($enseignant['nom'], $enseignant['prenom'], $enseignant['email'], 'password', 'enseignant');
            $this->createEnseignantProfile($user->id, $enseignant['nom'], $enseignant['prenom'], $enseignant['email']);
            echo "✅ Enseignant créé: {$enseignant['email']}\n";
        }

        // Créer les coordinateurs
        $promotions = DB::table('promotions')->get();
        $coordinateurs = [
            ['nom' => 'Bernard', 'prenom' => 'Sophie', 'email' => 'sophie.bernard@ifran.ci'],
            ['nom' => 'Dubois', 'prenom' => 'Michel', 'email' => 'michel.dubois@ifran.ci'],
            ['nom' => 'Moreau', 'prenom' => 'Claire', 'email' => 'claire.moreau@ifran.ci'],
        ];

        foreach ($coordinateurs as $index => $coordinateur) {
            $user = $this->createUser($coordinateur['nom'], $coordinateur['prenom'], $coordinateur['email'], 'password', 'coordinateur');
            $this->createCoordinateurProfile($user->id, $coordinateur['nom'], $coordinateur['prenom'], $coordinateur['email'], $promotions[$index]->id);
            echo "✅ Coordinateur créé: {$coordinateur['email']}\n";
        }

        // Créer les étudiants
        $classes = DB::table('classes')->get();
        $etudiants = [
            ['nom' => 'Konan', 'prenom' => 'Miyah', 'email' => 'miyah.konan@ifran.ci'],
            ['nom' => 'Bamba', 'prenom' => 'Aissatou', 'email' => 'aissatou.bamba@ifran.ci'],
            ['nom' => 'Kouassi', 'prenom' => 'Fatou', 'email' => 'fatou.kouassi@ifran.ci'],
            ['nom' => 'Traore', 'prenom' => 'Moussa', 'email' => 'moussa.traore@ifran.ci'],
            ['nom' => 'Diabate', 'prenom' => 'Aminata', 'email' => 'aminata.diabate@ifran.ci'],
            ['nom' => 'Ouattara', 'prenom' => 'Kadidja', 'email' => 'kadidja.ouattara@ifran.ci'],
            ['nom' => 'Yao', 'prenom' => 'Kouassi', 'email' => 'kouassi.yao@ifran.ci'],
            ['nom' => 'Kone', 'prenom' => 'Fatima', 'email' => 'fatima.kone@ifran.ci'],
        ];

        foreach ($etudiants as $index => $etudiant) {
            $user = $this->createUser($etudiant['nom'], $etudiant['prenom'], $etudiant['email'], 'password', 'etudiant');
            $this->createEtudiantProfile($user->id, $etudiant['nom'], $etudiant['prenom'], $etudiant['email'], $classes[$index % count($classes)]->id);
            echo "✅ Étudiant créé: {$etudiant['email']}\n";
        }

        // Créer les parents
        foreach ($etudiants as $etudiant) {
            $user = $this->createUser($etudiant['nom'], "Parent de {$etudiant['prenom']}", "parent.{$etudiant['email']}", 'password', 'parent');
            $this->createParentProfile($user->id, $etudiant['nom'], "Parent de {$etudiant['prenom']}", "parent.{$etudiant['email']}");
            echo "✅ Parent créé: parent.{$etudiant['email']}\n";
        }
    }

    private function createUser($nom, $prenom, $email, $password, $roleCode)
    {
        $existing = DB::table('users')->where('email', $email)->first();
        if ($existing) {
            return (object) $existing;
        }

        $userId = DB::table('users')->insertGetId([
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'password' => Hash::make($password),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $role = DB::table('roles')->where('code', $roleCode)->first();
        if ($role) {
            DB::table('role_user')->insert([
                'user_id' => $userId,
                'role_id' => $role->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return (object) ['id' => $userId, 'nom' => $nom, 'prenom' => $prenom, 'email' => $email];
    }

    private function createEnseignantProfile($userId, $nom, $prenom, $email)
    {
        $existing = DB::table('enseignants')->where('user_id', $userId)->first();
        if (!$existing) {
            DB::table('enseignants')->insert([
                'user_id' => $userId,
                'nom' => $nom,
                'prenom' => $prenom,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function createCoordinateurProfile($userId, $nom, $prenom, $email, $promotionId)
    {
        $existing = DB::table('coordinateurs')->where('user_id', $userId)->first();
        if (!$existing) {
            DB::table('coordinateurs')->insert([
                'user_id' => $userId,
                'nom' => $nom,
                'prenom' => $prenom,
                'promotion_id' => $promotionId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function createEtudiantProfile($userId, $nom, $prenom, $email, $classeId)
    {
        $existing = DB::table('etudiants')->where('user_id', $userId)->first();
        if (!$existing) {
            DB::table('etudiants')->insert([
                'user_id' => $userId,
                'nom' => $nom,
                'prenom' => $prenom,
                'classe_id' => $classeId,
                'date_naissance' => Carbon::now()->subYears(rand(18, 25)),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function createParentProfile($userId, $nom, $prenom, $email)
    {
        $existing = DB::table('parents')->where('user_id', $userId)->first();
        if (!$existing) {
            DB::table('parents')->insert([
                'user_id' => $userId,
                'nom' => $nom,
                'prenom' => $prenom,
                'telephone' => '+225' . rand(70000000, 79999999),
                'profession' => 'Ingénieur',
                'adresse' => 'Cocody, Abidjan',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function createSessionsDeCours()
    {
        echo "📅 Création des sessions de cours...\n";

        $annees = DB::table('annees_academiques')->get();
        $classes = DB::table('classes')->get();
        $matieres = DB::table('matieres')->get();
        $enseignants = DB::table('enseignants')->get();
        $typesCours = DB::table('types_cours')->get();
        $statutsSession = DB::table('statuts_session')->get();

        foreach ($annees as $annee) {
            // Créer les semestres pour cette année
            $semestres = $this->createSemestresForAnnee($annee->id, $annee->nom);

            foreach ($semestres as $semestre) {
                foreach ($classes as $classe) {
                    // Créer 3-5 sessions par classe par semestre
                    $nombreSessions = rand(3, 5);

                    for ($i = 0; $i < $nombreSessions; $i++) {
                        $matiere = $matieres->random();
                        $enseignant = $enseignants->random();
                        $typeCours = $typesCours->random();
                        $statut = $statutsSession->random();

                        // Générer des dates dans le semestre
                        $dateDebut = Carbon::parse($semestre->date_debut)->addDays(rand(0, 60));
                        $dateFin = $dateDebut->copy()->addHours(rand(1, 3));

                        $sessionId = DB::table('course_sessions')->insertGetId([
                            'semester_id' => $semestre->id,
                            'classe_id' => $classe->id,
                            'matiere_id' => $matiere->id,
                            'enseignant_id' => $enseignant->id,
                            'type_cours_id' => $typeCours->id,
                            'status_id' => $statut->id,
                            'start_time' => $dateDebut,
                            'end_time' => $dateFin,
                            'location' => 'Salle ' . chr(65 + rand(0, 5)) . rand(100, 200),
                            'notes' => "Session de {$matiere->nom} - {$typeCours->nom}",
                            'annee_academique_id' => $annee->id,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        echo "✅ Session créée: {$matiere->nom} - {$classe->nom} - {$dateDebut->format('d/m/Y H:i')}\n";
                    }
                }
            }
        }
    }

    private function createSemestresForAnnee($anneeId, $anneeNom)
    {
        $anneeDebut = explode('-', $anneeNom)[0];

        $semestres = [
            [
                'nom' => 'Semestre 1',
                'date_debut' => Carbon::create($anneeDebut, 9, 1),
                'date_fin' => Carbon::create($anneeDebut + 1, 1, 31),
            ],
            [
                'nom' => 'Semestre 2',
                'date_debut' => Carbon::create($anneeDebut + 1, 2, 1),
                'date_fin' => Carbon::create($anneeDebut + 1, 6, 30),
            ]
        ];

        $createdSemestres = [];
        foreach ($semestres as $semestre) {
            $existing = DB::table('semestres')
                ->where('nom', $semestre['nom'])
                ->where('annee_academique_id', $anneeId)
                ->first();

            if (!$existing) {
                $semestreId = DB::table('semestres')->insertGetId([
                    'nom' => $semestre['nom'],
                    'annee_academique_id' => $anneeId,
                    'date_debut' => $semestre['date_debut'],
                    'date_fin' => $semestre['date_fin'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $createdSemestres[] = (object) ['id' => $semestreId, 'date_debut' => $semestre['date_debut'], 'date_fin' => $semestre['date_fin']];
            } else {
                $createdSemestres[] = $existing;
            }
        }

        return $createdSemestres;
    }

    private function createPresences()
    {
        echo "📝 Création des présences...\n";

        $sessions = DB::table('course_sessions')->get();
        $etudiants = DB::table('etudiants')->get();
        $statutsPresence = DB::table('statuts_presence')->get();

        foreach ($sessions as $session) {
            // Récupérer les étudiants de cette classe
            $etudiantsClasse = $etudiants->where('classe_id', $session->classe_id);

            foreach ($etudiantsClasse as $etudiant) {
                $statutPresence = $statutsPresence->random();

                DB::table('presences')->insert([
                    'course_session_id' => $session->id,
                    'etudiant_id' => $etudiant->id,
                    'statut_presence_id' => $statutPresence->id,
                    'enregistre_le' => Carbon::now(),
                    'enregistre_par_user_id' => 1, // Admin
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            echo "✅ Présences créées pour la session: {$session->id}\n";
        }
    }

    private function createEnseignantMatiereAssociations()
    {
        echo "🔗 Création des associations enseignant-matière...\n";

        $enseignants = DB::table('enseignants')->get();
        $matieres = DB::table('matieres')->get();

        foreach ($enseignants as $enseignant) {
            // Associer 2-4 matières par enseignant
            $matieresEnseignant = $matieres->random(rand(2, 4));

            foreach ($matieresEnseignant as $matiere) {
                $existing = DB::table('enseignant_matiere')
                    ->where('enseignant_id', $enseignant->id)
                    ->where('matiere_id', $matiere->id)
                    ->first();

                if (!$existing) {
                    DB::table('enseignant_matiere')->insert([
                        'enseignant_id' => $enseignant->id,
                        'matiere_id' => $matiere->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
            echo "✅ Associations créées pour l'enseignant: {$enseignant->nom} {$enseignant->prenom}\n";
        }
    }
}
