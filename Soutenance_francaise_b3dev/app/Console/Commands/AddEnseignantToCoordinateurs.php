php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Coordinateur;
use App\Models\Enseignant;
use App\Models\User;

class AddEnseignantToCoordinateurs extends Command
{
    protected $signature = 'coordinateurs:add-enseignant';
    protected $description = 'Ajouter des profils enseignants aux coordinateurs existants';

    public function handle()
    {
        $this->info('Ajout des profils enseignants aux coordinateurs...');

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

                $this->info("✅ Profil enseignant créé pour {$coordinateur->prenom} {$coordinateur->nom}");
                $count++;
            } else {
                $this->line("⏭️  Profil enseignant déjà existant pour {$coordinateur->prenom} {$coordinateur->nom}");
            }
        }

        $this->info("\n🎉 {$count} profils enseignants ont été créés pour les coordinateurs.");
        $this->info('Les coordinateurs peuvent maintenant créer des sessions Workshop et E-learning.');
    }
}
