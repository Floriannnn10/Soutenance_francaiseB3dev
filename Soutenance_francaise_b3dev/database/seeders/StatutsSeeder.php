<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StatutPresence;
use App\Models\StatutSession;

class StatutsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Statuts de présence
        $statutsPresence = [
            ['name' => 'present', 'display_name' => 'Présent'],
            ['name' => 'absent', 'display_name' => 'Absent'],
            ['name' => 'late', 'display_name' => 'En retard'],
        ];

        foreach ($statutsPresence as $statut) {
            StatutPresence::create($statut);
        }

        // Statuts de session
        $statutsSession = [
            ['name' => 'scheduled', 'display_name' => 'Prévue'],
            ['name' => 'cancelled', 'display_name' => 'Annulée'],
            ['name' => 'postponed', 'display_name' => 'Reportée'],
            ['name' => 'completed', 'display_name' => 'Terminée'],
        ];

        foreach ($statutsSession as $statut) {
            StatutSession::create($statut);
        }
    }
}
