<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\ParentEtudiant;
use App\Models\Etudiant;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class CreateParentWithChildrenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer le rôle parent s'il n'existe pas
        $roleParent = Role::firstOrCreate(
            ['code' => 'parent'],
            [
                'nom' => 'Parent',
                'code' => 'parent',
                'description' => 'Parent d\'étudiant'
            ]
        );

        // Créer l'utilisateur parent
        $user = User::firstOrCreate(
            ['email' => 'parent@ifran.com'],
            [
                'nom' => 'Dupont',
                'prenom' => 'Marie',
                'email' => 'parent@ifran.com',
                'password' => Hash::make('password'),
            ]
        );

        // Attacher le rôle parent à l'utilisateur
        if (!$user->roles()->where('code', 'parent')->exists()) {
            $user->roles()->attach($roleParent->id);
        }

        // Créer le profil parent
        $parent = ParentEtudiant::firstOrCreate(
            ['user_id' => $user->id],
            [
                'user_id' => $user->id,
                'nom' => 'Dupont',
                'prenom' => 'Marie',
                'telephone' => '0123456789',
                'profession' => 'Enseignante',
                'adresse' => '123 Rue de la Paix, Paris',
            ]
        );

        // Récupérer quelques étudiants existants pour les associer
        $etudiants = Etudiant::take(3)->get();

        if ($etudiants->count() > 0) {
            // Associer les étudiants au parent
            $parent->etudiants()->sync($etudiants->pluck('id'));

            $this->command->info("✅ Parent créé avec succès !");
            $this->command->info("📧 Email: parent@ifran.com");
            $this->command->info("🔑 Mot de passe: password");
            $this->command->info("👶 Enfants associés: " . $etudiants->count());
        } else {
            $this->command->warn("⚠️ Aucun étudiant trouvé pour associer au parent");
        }
    }
}
