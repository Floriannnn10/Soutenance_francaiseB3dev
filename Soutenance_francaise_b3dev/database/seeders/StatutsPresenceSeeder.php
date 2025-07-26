<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StatutPresence;

class StatutsPresenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuts = [
            [
                'nom' => 'Présent',
                'code' => 'present',
                'description' => 'L\'étudiant est présent au cours'
            ],
            [
                'nom' => 'Absent',
                'code' => 'absent',
                'description' => 'L\'étudiant est absent au cours'
            ],
            [
                'nom' => 'En retard',
                'code' => 'retard',
                'description' => 'L\'étudiant est arrivé en retard au cours'
            ]
        ];

        foreach ($statuts as $statut) {
            StatutPresence::create($statut);
        }
    }
}
