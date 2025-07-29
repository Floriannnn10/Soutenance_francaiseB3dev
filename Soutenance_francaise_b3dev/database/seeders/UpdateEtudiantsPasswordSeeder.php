<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Etudiant;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UpdateEtudiantsPasswordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Mise à jour des mots de passe des étudiants existants...');

        // Récupérer tous les étudiants qui n'ont pas de mot de passe
        $etudiants = Etudiant::whereNull('password')->orWhere('password', '')->get();

        if ($etudiants->isEmpty()) {
            $this->command->info('Tous les étudiants ont déjà un mot de passe.');
            return;
        }

        $count = 0;

        foreach ($etudiants as $etudiant) {
            // Mettre à jour le mot de passe
            $etudiant->update([
                'password' => Hash::make('password')
            ]);

            $count++;
            $this->command->info("✅ Mot de passe mis à jour pour {$etudiant->prenom} {$etudiant->nom}");
        }

        $this->command->info("🎉 {$count} étudiants ont été mis à jour avec le mot de passe 'password'");
        $this->command->info('📝 Tous les étudiants peuvent maintenant se connecter avec le mot de passe : password');
    }
}
