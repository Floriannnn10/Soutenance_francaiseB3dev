<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StatutSession;

class StatutsSessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuts = [
            [
                'nom' => 'Planifiée',
                'code' => 'planifiee',
                'description' => 'La session est planifiée mais n\'a pas encore eu lieu'
            ],
            [
                'nom' => 'En cours',
                'code' => 'en_cours',
                'description' => 'La session est en cours'
            ],
            [
                'nom' => 'Terminée',
                'code' => 'terminee',
                'description' => 'La session est terminée'
            ],
            [
                'nom' => 'Annulée',
                'code' => 'annulee',
                'description' => 'La session a été annulée'
            ],
            [
                'nom' => 'Reportée',
                'code' => 'reportee',
                'description' => 'La session a été reportée'
            ]
        ];

        foreach ($statuts as $statut) {
            StatutSession::create($statut);
        }
    }
}
