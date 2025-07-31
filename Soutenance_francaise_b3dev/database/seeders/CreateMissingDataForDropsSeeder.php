<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\ParentEtudiant;
use App\Models\Coordinateur;
use App\Models\Enseignant;
use App\Models\Etudiant;
use App\Models\Classe;
use App\Models\Promotion;
use Carbon\Carbon;

class CreateMissingDataForDropsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🔧 Création des données manquantes pour les notifications de drops...');

        // Récupérer les étudiants créés
        $etudiants = Etudiant::where('email', 'like', '%dropped%')->get();

        if ($etudiants->isEmpty()) {
            $this->command->error('❌ Aucun étudiant en situation de dropping trouvé.');
            return;
        }

        $this->command->info("📚 Trouvé {$etudiants->count()} étudiants en situation de dropping.");

        // Créer des parents pour chaque étudiant
        foreach ($etudiants as $etudiant) {
            $this->createParentForStudent($etudiant);
        }

        // Créer un coordinateur pour la classe
        $this->createCoordinateurForClass($etudiants->first()->classe);

        // Créer un enseignant pour la matière
        $this->createEnseignantForMatiere();

        $this->command->info('✅ Données manquantes créées avec succès !');
        $this->command->info('🔧 Relancez maintenant: php artisan drops:process-automatic');
    }

    private function createParentForStudent($etudiant)
    {
        $timestamp = time() + rand(1, 1000);
        $parentName = "Parent de {$etudiant->prenom}";

        // Créer l'utilisateur parent
        $user = User::create([
            'nom' => $parentName,
            'prenom' => 'Parent',
            'email' => "parent.{$etudiant->prenom}.{$timestamp}@test.com",
            'password' => bcrypt('password'),
        ]);

        // Attacher le rôle parent
        $roleParent = Role::where('code', 'parent')->first();
        if ($roleParent) {
            $user->roles()->attach($roleParent->id);
        }

        // Créer le profil parent
        $parent = ParentEtudiant::create([
            'user_id' => $user->id,
            'nom' => $parentName,
            'prenom' => 'Parent',
            'email' => $user->email,
            'telephone' => '0123456789',
        ]);

        // Associer le parent à l'étudiant
        $etudiant->parents()->attach($parent->id);

        $this->command->info("👨‍👩‍👧‍👦 Parent créé pour {$etudiant->prenom} {$etudiant->nom} - Email: {$user->email}");
    }

    private function createCoordinateurForClass($classe)
    {
        $timestamp = time() + rand(1, 1000);

        // Créer l'utilisateur coordinateur
        $user = User::create([
            'nom' => 'Coordinateur',
            'prenom' => 'Test',
            'email' => "coordinateur.test.{$timestamp}@test.com",
            'password' => bcrypt('password'),
        ]);

        // Attacher le rôle coordinateur
        $roleCoordinateur = Role::where('code', 'coordinateur')->first();
        if ($roleCoordinateur) {
            $user->roles()->attach($roleCoordinateur->id);
        }

        // Créer une promotion si elle n'existe pas
        $promotion = Promotion::first();
        if (!$promotion) {
            $promotion = Promotion::create([
                'nom' => 'Promotion Test',
                'annee_academique_id' => \App\Models\AnneeAcademique::getActive()->id,
            ]);
        }

        // Créer le profil coordinateur
        $coordinateur = Coordinateur::create([
            'user_id' => $user->id,
            'nom' => 'Coordinateur',
            'prenom' => 'Test',
            'email' => $user->email,
            'promotion_id' => $promotion->id,
        ]);

        $this->command->info("👨‍💼 Coordinateur créé - Email: {$user->email}");
    }

    private function createEnseignantForMatiere()
    {
        $timestamp = time() + rand(1, 1000);

        // Créer l'utilisateur enseignant
        $user = User::create([
            'nom' => 'Enseignant',
            'prenom' => 'Test',
            'email' => "enseignant.test.{$timestamp}@test.com",
            'password' => bcrypt('password'),
        ]);

        // Attacher le rôle enseignant
        $roleEnseignant = Role::where('code', 'enseignant')->first();
        if ($roleEnseignant) {
            $user->roles()->attach($roleEnseignant->id);
        }

        // Créer le profil enseignant
        $enseignant = Enseignant::create([
            'user_id' => $user->id,
            'nom' => 'Enseignant',
            'prenom' => 'Test',
            'email' => $user->email,
        ]);

        $this->command->info("👨‍🏫 Enseignant créé - Email: {$user->email}");
    }
}
