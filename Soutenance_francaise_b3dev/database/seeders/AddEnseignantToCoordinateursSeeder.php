<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Coordinateur;
use App\Models\Enseignant;
use App\Models\User;

class AddEnseignantToCoordinateursSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Ajout des profils enseignants aux coordinateurs...');

        $coordinateurs = Coordinateur::with('user')->get();
        $count = 0;

        foreach ($coordinateurs as $coordinateur) {
            // Vérifier si le coordinateur a déjà un profil enseignant
            $enseignantExistant = Enseignant::where('user_id', $coordinateur->user_id)->first();

            if (!$enseignantExistant) {
                // Créer le profil enseignant pour ce coordinateur
                Enseignant::create([
                    'user_id' => $coordinateur->user_id,
                    'nom' => $coordinateur->nom,
                    'prenom' => $coordinateur->prenom,
                    'email' => $coordinateur->email,
                ]);

                $this->command->info("✅ Profil enseignant créé pour {$coordinateur->prenom} {$coordinateur->nom}");
                $count++;
            } else {
                $this->command->line("⏭️  Profil enseignant déjà existant pour {$coordinateur->prenom} {$coordinateur->nom}");
            }
        }

        $this->command->info("\n🎉 {$count} profils enseignants ont été créés pour les coordinateurs.");
        $this->command->info('Les coordinateurs peuvent maintenant créer des sessions Workshop et E-learning.');
    }
}
