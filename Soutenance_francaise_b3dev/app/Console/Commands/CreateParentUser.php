<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Role;
use App\Models\ParentEtudiant;
use App\Models\Etudiant;

class CreateParentUser extends Command
{
    protected $signature = 'user:create-parent';
    protected $description = 'Créer un utilisateur parent avec ses relations';

    public function handle()
    {
        $this->info('👨‍👩‍👧‍👦 Création de l\'utilisateur parent...');

        // Vérifier si l'utilisateur existe déjà
        $user = User::where('email', 'babe@ifran.com')->first();

        if ($user) {
            $this->warn("⚠️  L'utilisateur babe@ifran.com existe déjà (ID: {$user->id})");
        } else {
            // Créer l'utilisateur parent
            $user = User::create([
                'nom' => 'Manou',
                'prenom' => 'Babe',
                'email' => 'babe@ifran.com',
                'password' => bcrypt('password'),
            ]);
            $this->info("✅ Utilisateur créé (ID: {$user->id})");
        }

        // Vérifier et ajouter le rôle parent
        $roleParent = Role::where('code', 'parent')->first();
        if (!$roleParent) {
            $this->error('❌ Rôle parent non trouvé');
            return 1;
        }

        $hasParentRole = $user->roles()->where('code', 'parent')->exists();
        if (!$hasParentRole) {
            $user->roles()->attach($roleParent->id);
            $this->info("✅ Rôle parent ajouté");
        } else {
            $this->info("✅ Rôle parent déjà présent");
        }

        // Créer l'enregistrement ParentEtudiant
        $parentRecord = ParentEtudiant::where('user_id', $user->id)->first();

        if (!$parentRecord) {
            $parentRecord = ParentEtudiant::create([
                'user_id' => $user->id,
                'nom' => 'Manou',
                'prenom' => 'Babe',
                'telephone' => '+22507000000',
                'profession' => 'Parent',
                'adresse' => 'Non spécifiée',
                'photo' => null
            ]);
            $this->info("✅ Enregistrement ParentEtudiant créé (ID: {$parentRecord->id})");
        } else {
            $this->info("✅ Enregistrement ParentEtudiant déjà existant (ID: {$parentRecord->id})");
        }

        // Associer avec un étudiant existant
        $enfants = $parentRecord->etudiants;
        if ($enfants->count() == 0) {
            $etudiant = Etudiant::first();
            if ($etudiant) {
                $parentRecord->etudiants()->attach($etudiant->id);
                $this->info("✅ Associé avec l'étudiant: {$etudiant->prenom} {$etudiant->nom}");
            } else {
                $this->warn("⚠️  Aucun étudiant disponible pour l'association");
            }
        } else {
            $this->info("✅ Déjà associé avec {$enfants->count()} enfant(s)");
        }

        $this->info("\n🎉 Utilisateur parent créé avec succès!");
        $this->info("Email: babe@ifran.com");
        $this->info("Mot de passe: password");
        $this->info("Vous pouvez maintenant vous connecter et tester la page 'Mes enfants'.");

        return 0;
    }
}
