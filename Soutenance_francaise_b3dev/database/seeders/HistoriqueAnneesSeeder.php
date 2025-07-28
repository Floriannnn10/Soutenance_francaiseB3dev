<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HistoriqueAnneesSeeder extends Seeder
{
    public function run(): void
    {
        echo "=== Création des données historiques pour les années passées ===\n";

        // Créer les années académiques passées
        $anneesPassées = [
            ['nom' => '2020-2021', 'statut' => 'Terminée', 'date_debut' => '2020-09-01', 'date_fin' => '2021-08-31'],
            ['nom' => '2021-2022', 'statut' => 'Terminée', 'date_debut' => '2021-09-01', 'date_fin' => '2022-08-31'],
            ['nom' => '2022-2023', 'statut' => 'Terminée', 'date_debut' => '2022-09-01', 'date_fin' => '2023-08-31'],
            ['nom' => '2023-2024', 'statut' => 'Terminée', 'date_debut' => '2023-09-01', 'date_fin' => '2024-08-31'],
        ];

        foreach ($anneesPassées as $annee) {
            // Vérifier si l'année existe déjà
            $existingAnnee = DB::table('annees_academiques')->where('nom', $annee['nom'])->first();

            if (!$existingAnnee) {
                $anneeId = DB::table('annees_academiques')->insertGetId([
                    'nom' => $annee['nom'],
                    'statut' => $annee['statut'],
                    'date_debut' => $annee['date_debut'],
                    'date_fin' => $annee['date_fin'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                echo "✅ Année académique créée: {$annee['nom']} ({$annee['statut']})\n";

                // Créer les semestres pour cette année
                $this->createSemestresForAnnee($anneeId, $annee['nom']);
            } else {
                echo "ℹ️ Année académique existe déjà: {$annee['nom']}\n";
            }
        }

        echo "\n=== Données historiques créées avec succès ===\n";
    }

    private function createSemestresForAnnee($anneeId, $anneeNom)
    {
        $semestres = [
            [
                'nom' => 'Semestre 1',
                'date_debut' => Carbon::parse($anneeNom . '-09-01'),
                'date_fin' => Carbon::parse($anneeNom . '-01-31'),
                'statut' => 'Terminé'
            ],
            [
                'nom' => 'Semestre 2',
                'date_debut' => Carbon::parse($anneeNom . '-02-01'),
                'date_fin' => Carbon::parse($anneeNom . '-06-30'),
                'statut' => 'Terminé'
            ]
        ];

        foreach ($semestres as $semestre) {
            // Vérifier si le semestre existe déjà
            $existingSemestre = DB::table('semestres')
                ->where('nom', $semestre['nom'])
                ->where('annee_academique_id', $anneeId)
                ->first();

            if (!$existingSemestre) {
                DB::table('semestres')->insert([
                    'nom' => $semestre['nom'],
                    'annee_academique_id' => $anneeId,
                    'date_debut' => $semestre['date_debut'],
                    'date_fin' => $semestre['date_fin'],
                    'statut' => $semestre['statut'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                echo "  ✅ Semestre créé: {$semestre['nom']} ({$semestre['statut']})\n";
            } else {
                echo "  ℹ️ Semestre existe déjà: {$semestre['nom']}\n";
            }
        }
    }
}
