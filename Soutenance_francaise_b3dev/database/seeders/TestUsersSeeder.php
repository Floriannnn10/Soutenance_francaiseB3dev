<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\Enseignant;
use App\Models\Etudiant;
use App\Models\ParentEtudiant;
use App\Models\Coordinateur;
use App\Models\Classe;
use App\Models\Promotion;
use App\Models\Matiere;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Création des utilisateurs de test...');

        // Récupérer les rôles
        $adminRole = Role::where('code', 'admin')->first();
        $enseignantRole = Role::where('code', 'enseignant')->first();
        $etudiantRole = Role::where('code', 'etudiant')->first();
        $parentRole = Role::where('code', 'parent')->first();
        $coordinateurRole = Role::where('code', 'coordinateur')->first();

        // Créer l'admin de test
        $this->createAdmin($adminRole);

        // Créer les enseignants de test
        $this->createEnseignants($enseignantRole);

        // Créer les étudiants de test
        $this->createEtudiants($etudiantRole);

        // Créer les parents de test
        $this->createParents($parentRole);

        // Créer les coordinateurs de test
        $this->createCoordinateurs($coordinateurRole);

        $this->command->info('✅ Tous les utilisateurs de test ont été créés avec succès!');
    }

    private function createAdmin($adminRole)
    {
        $admin = User::create([
            'nom' => 'Admin',
            'prenom' => 'Système',
            'email' => 'admin@ifran.ci',
            'password' => Hash::make('password'),
        ]);

        $admin->roles()->attach($adminRole->id);
        $this->command->info('✅ Admin créé: admin@ifran.ci (password)');
    }

    private function createEnseignants($enseignantRole)
    {
        $enseignants = [
            [
                'nom' => 'Banga',
                'prenom' => 'Florian',
                'email' => 'florian@ifran.ci',
                'password' => 'password',
            ],
            [
                'nom' => 'Dupont',
                'prenom' => 'Jean',
                'email' => 'jean.dupont@ifran.ci',
                'password' => 'password',
            ],
            [
                'nom' => 'Martin',
                'prenom' => 'Marie',
                'email' => 'marie.martin@ifran.ci',
                'password' => 'password',
            ],
            [
                'nom' => 'Bernard',
                'prenom' => 'Pierre',
                'email' => 'pierre.bernard@ifran.ci',
                'password' => 'password',
            ],
        ];

        foreach ($enseignants as $enseignantData) {
            $user = User::create([
                'nom' => $enseignantData['nom'],
                'prenom' => $enseignantData['prenom'],
                'email' => $enseignantData['email'],
                'password' => Hash::make($enseignantData['password']),
            ]);

            $user->roles()->attach($enseignantRole->id);

            $enseignant = Enseignant::create([
                'user_id' => $user->id,
                'nom' => $enseignantData['nom'],
                'prenom' => $enseignantData['prenom'],
            ]);

            // Attribuer quelques matières
            $matieres = Matiere::take(3)->get();
            if ($matieres->isNotEmpty()) {
                $enseignant->matieres()->attach($matieres->pluck('id'));
            }

            $this->command->info("✅ Enseignant créé: {$enseignantData['email']} (password)");
        }
    }

    private function createEtudiants($etudiantRole)
    {
        $classes = Classe::all();

        if ($classes->isEmpty()) {
            $this->command->warn('Aucune classe disponible pour créer des étudiants.');
            return;
        }

        $etudiants = [
            [
                'nom' => 'Konan',
                'prenom' => 'Miyah',
                'email' => 'miyah.konan@ifran.ci',
                'password' => 'password',
                'date_naissance' => '2005-05-12',
            ],
            [
                'nom' => 'Bamba',
                'prenom' => 'Aissatou',
                'email' => 'aissatou.bamba@ifran.ci',
                'password' => 'password',
                'date_naissance' => '2004-08-15',
            ],
            [
                'nom' => 'Kouassi',
                'prenom' => 'Fatou',
                'email' => 'fatou.kouassi@ifran.ci',
                'password' => 'password',
                'date_naissance' => '2005-03-22',
            ],
            [
                'nom' => 'Traore',
                'prenom' => 'Moussa',
                'email' => 'moussa.traore@ifran.ci',
                'password' => 'password',
                'date_naissance' => '2004-11-08',
            ],
            [
                'nom' => 'Diabate',
                'prenom' => 'Aminata',
                'email' => 'aminata.diabate@ifran.ci',
                'password' => 'password',
                'date_naissance' => '2005-01-30',
            ],
        ];

        foreach ($etudiants as $index => $etudiantData) {
            $user = User::create([
                'nom' => $etudiantData['nom'],
                'prenom' => $etudiantData['prenom'],
                'email' => $etudiantData['email'],
                'password' => Hash::make($etudiantData['password']),
            ]);

            $user->roles()->attach($etudiantRole->id);

            $classe = $classes[$index % $classes->count()];

            $etudiant = Etudiant::create([
                'user_id' => $user->id,
                'classe_id' => $classe->id,
                'nom' => $etudiantData['nom'],
                'prenom' => $etudiantData['prenom'],
                'email' => $etudiantData['email'],
                'date_naissance' => $etudiantData['date_naissance'],
            ]);

            $this->command->info("✅ Étudiant créé: {$etudiantData['email']} (password) - Classe: {$classe->nom}");
        }
    }

    private function createParents($parentRole)
    {
        $etudiants = Etudiant::all();

        if ($etudiants->isEmpty()) {
            $this->command->warn('Aucun étudiant disponible pour créer des parents.');
            return;
        }

        $parents = [
            [
                'nom' => 'Konan',
                'prenom' => 'Parent de Miyah',
                'email' => 'parent.miyah.konan@ifran.ci',
                'password' => 'password',
                'telephone' => '+22507000001',
                'profession' => 'Ingénieur',
                'adresse' => 'Cocody Angré',
            ],
            [
                'nom' => 'Bamba',
                'prenom' => 'Parent d\'Aissatou',
                'email' => 'parent.aissatou.bamba@ifran.ci',
                'password' => 'password',
                'telephone' => '+22507000002',
                'profession' => 'Médecin',
                'adresse' => 'Marcory',
            ],
            [
                'nom' => 'Kouassi',
                'prenom' => 'Parent de Fatou',
                'email' => 'parent.fatou.kouassi@ifran.ci',
                'password' => 'password',
                'telephone' => '+22507000003',
                'profession' => 'Avocat',
                'adresse' => 'Riviera',
            ],
        ];

        foreach ($parents as $index => $parentData) {
            $user = User::create([
                'nom' => $parentData['nom'],
                'prenom' => $parentData['prenom'],
                'email' => $parentData['email'],
                'password' => Hash::make($parentData['password']),
            ]);

            $user->roles()->attach($parentRole->id);

            $parent = ParentEtudiant::create([
                'user_id' => $user->id,
                'nom' => $parentData['nom'],
                'prenom' => $parentData['prenom'],
                'telephone' => $parentData['telephone'],
                'profession' => $parentData['profession'],
                'adresse' => $parentData['adresse'],
            ]);

            // Associer à un étudiant
            $etudiant = $etudiants[$index % $etudiants->count()];
            $parent->etudiants()->attach($etudiant->id);

            $this->command->info("✅ Parent créé: {$parentData['email']} (password) - Étudiant: {$etudiant->prenom} {$etudiant->nom}");
        }
    }

    private function createCoordinateurs($coordinateurRole)
    {
        $promotions = Promotion::all();

        if ($promotions->isEmpty()) {
            $this->command->warn('Aucune promotion disponible pour créer des coordinateurs.');
            return;
        }

        $coordinateurs = [
            [
                'nom' => 'Bernard',
                'prenom' => 'Sophie',
                'email' => 'sophie.bernard@ifran.ci',
                'password' => 'password',
            ],
            [
                'nom' => 'Dubois',
                'prenom' => 'Michel',
                'email' => 'michel.dubois@ifran.ci',
                'password' => 'password',
            ],
            [
                'nom' => 'Moreau',
                'prenom' => 'Claire',
                'email' => 'claire.moreau@ifran.ci',
                'password' => 'password',
            ],
        ];

        foreach ($coordinateurs as $index => $coordinateurData) {
            $user = User::create([
                'nom' => $coordinateurData['nom'],
                'prenom' => $coordinateurData['prenom'],
                'email' => $coordinateurData['email'],
                'password' => Hash::make($coordinateurData['password']),
            ]);

            $user->roles()->attach($coordinateurRole->id);

            $promotion = $promotions[$index % $promotions->count()];

            $coordinateur = Coordinateur::create([
                'user_id' => $user->id,
                'nom' => $coordinateurData['nom'],
                'prenom' => $coordinateurData['prenom'],
                'email' => $coordinateurData['email'],
                'promotion_id' => $promotion->id,
            ]);

            $this->command->info("✅ Coordinateur créé: {$coordinateurData['email']} (password) - Promotion: {$promotion->nom}");
        }
    }
}
