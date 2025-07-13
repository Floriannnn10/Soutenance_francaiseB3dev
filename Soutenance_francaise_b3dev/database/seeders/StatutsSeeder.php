<?php

namespace Database\Seeders;

use App\Models\StatutPresence;
use App\Models\StatutSession;
use App\Models\TypeNotification;
use App\Models\TypeCours;
use Illuminate\Database\Seeder;

class StatutsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Statuts de présence
        $statutsPresence = [
            [
                'nom' => StatutPresence::PRESENT,
                'couleur' => '#10B981', // Vert
            ],
            [
                'nom' => StatutPresence::EN_RETARD,
                'couleur' => '#F59E0B', // Orange
            ],
            [
                'nom' => StatutPresence::ABSENT,
                'couleur' => '#EF4444', // Rouge
            ],
        ];

        foreach ($statutsPresence as $statut) {
            StatutPresence::create($statut);
        }

        // Statuts de session
        $statutsSession = [
            [
                'nom' => StatutSession::PREVUE,
                'couleur' => '#3B82F6', // Bleu
            ],
            [
                'nom' => StatutSession::ANNULEE,
                'couleur' => '#EF4444', // Rouge
            ],
            [
                'nom' => StatutSession::REPORTEE,
                'couleur' => '#F59E0B', // Orange
            ],
            [
                'nom' => StatutSession::TERMINEE,
                'couleur' => '#10B981', // Vert
            ],
        ];

        foreach ($statutsSession as $statut) {
            StatutSession::create($statut);
        }

        // Types de notification
        $typesNotification = [
            [
                'nom' => TypeNotification::DROPPE,
                'icone' => 'warning',
            ],
            [
                'nom' => TypeNotification::ANNULATION_COURS,
                'icone' => 'error',
            ],
            [
                'nom' => TypeNotification::INFORMATION,
                'icone' => 'info',
            ],
        ];

        foreach ($typesNotification as $type) {
            TypeNotification::create($type);
        }

        // Types de cours
        $typesCours = [
            [
                'nom' => TypeCours::CM,
                'description' => 'Cours Magistral',
            ],
            [
                'nom' => TypeCours::TD,
                'description' => 'Travaux Dirigés',
            ],
            [
                'nom' => TypeCours::TP,
                'description' => 'Travaux Pratiques',
            ],
            [
                'nom' => TypeCours::EXAMEN,
                'description' => 'Examen',
            ],
            [
                'nom' => TypeCours::CONTROLE,
                'description' => 'Contrôle',
            ],
        ];

        foreach ($typesCours as $type) {
            TypeCours::create($type);
        }
    }
}
