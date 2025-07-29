<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Coordinateur;
use App\Models\Enseignant;

class AddEnseignantToCoordinateursSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
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
                ]);

                $this->command->info("Profil enseignant cree pour {$coordinateur->prenom} {$coordinateur->nom}");
                $count++;
            } else {
                $this->command->line("Profil enseignant deja existant pour {$coordinateur->prenom} {$coordinateur->nom}");
            }
        }

        $this->command->info("{$count} profils enseignants ont ete crees pour les coordinateurs.");
        $this->command->info('Les coordinateurs peuvent maintenant creer des sessions Workshop et E-learning.');
    }
}
