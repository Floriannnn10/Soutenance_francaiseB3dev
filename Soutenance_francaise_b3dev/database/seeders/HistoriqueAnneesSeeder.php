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
            ['nom' => '2020-2021', 'date_debut' => '2020-09-01', 'date_fin' => '2021-08-31'],
            ['nom' => '2021-2022', 'date_debut' => '2021-09-01', 'date_fin' => '2022-08-31'],
            ['nom' => '2022-2023', 'date_debut' => '2022-09-01', 'date_fin' => '2023-08-31'],
            ['nom' => '2023-2024', 'date_debut' => '2023-09-01', 'date_fin' => '2024-08-31'],
        ];

        foreach ($anneesPassées as $annee) {
            // Vérifier si l'année existe déjà
            $existingAnnee = DB::table('annees_academiques')->where('nom', $annee['nom'])->first();

            if (!$existingAnnee) {
                $anneeId = DB::table('annees_academiques')->insertGetId([
                    'nom' => $annee['nom'],
                    'date_debut' => $annee['date_debut'],
                    'date_fin' => $annee['date_fin'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                echo "✅ Année académique créée: {$annee['nom']}\n";

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
        // Extraire l'année de début (2020 de "2020-2021")
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
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                echo "  ✅ Semestre créé: {$semestre['nom']}\n";
            } else {
                echo "  ℹ️ Semestre existe déjà: {$semestre['nom']}\n";
            }
        }
    }
}
