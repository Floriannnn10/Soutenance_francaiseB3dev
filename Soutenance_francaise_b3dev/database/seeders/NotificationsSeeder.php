<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Notification;
use App\Models\User;

class NotificationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        $notifications = [
            [
                'message' => 'Cours de mathématiques annulé pour demain',
                'type' => 'annulation',
            ],
            [
                'message' => 'Nouveau planning disponible',
                'type' => 'information',
            ],
            [
                'message' => 'Résultats des examens disponibles',
                'type' => 'resultat',
            ],
            [
                'message' => 'Réunion pédagogique prévue vendredi',
                'type' => 'reunion',
            ],
            [
                'message' => 'Maintenance système prévue ce weekend',
                'type' => 'maintenance',
            ],
        ];

        foreach ($notifications as $notificationData) {
            $notification = Notification::create($notificationData);

            // Attacher des utilisateurs aléatoires à chaque notification
            $usersAleatoires = $users->random(rand(3, 8));
            $notification->utilisateurs()->attach($usersAleatoires->pluck('id')->toArray());
        }
    }
}
