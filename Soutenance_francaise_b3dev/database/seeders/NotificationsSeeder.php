<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\TypeNotification;
use App\Models\User;
use Illuminate\Database\Seeder;

class NotificationsSeeder extends Seeder
{
    public function run(): void
    {
        $typeDroppe = TypeNotification::where('nom', TypeNotification::DROPPE)->first();
        $typeInfo = TypeNotification::where('nom', TypeNotification::INFORMATION)->first();
        $users = User::all();

        $notifications = [
            [
                'type_notification_id' => $typeDroppe->id,
                'message' => 'L\'étudiant Jean Dupont a un taux de présence inférieur à 30% en Mathématiques.',
                'donnees_supplementaires' => [
                    'etudiant_id' => 1,
                    'matiere_id' => 1,
                    'taux_presence' => 25,
                ],
            ],
            [
                'type_notification_id' => $typeInfo->id,
                'message' => 'Nouvelle session de cours programmée pour demain.',
                'donnees_supplementaires' => [
                    'session_id' => 1,
                    'date' => now()->addDay()->format('Y-m-d'),
                ],
            ],
        ];

        foreach ($notifications as $notificationData) {
            $notification = Notification::create($notificationData);

            // Associer la notification à tous les utilisateurs
            foreach ($users as $user) {
                $notification->utilisateurs()->attach($user->id);
            }
        }
    }
}
