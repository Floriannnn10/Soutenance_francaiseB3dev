<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CustomNotification;
use App\Models\User;

class TestNotifications extends Command
{
    protected $signature = 'notifications:test';
    protected $description = 'Créer une notification de test pour vérifier le système';

    public function handle()
    {
        $this->info('🧪 Test des Notifications de Drop');

        // Compter les notifications existantes
        $count = CustomNotification::count();
        $this->info("Notifications existantes: {$count}");

        // Trouver un utilisateur dropped
        $user = User::where('email', 'like', '%dropped%')->first();

        if ($user) {
            $this->info("Utilisateur trouvé: {$user->email}");

            // Créer une notification de test
            $notification = CustomNotification::create([
                'message' => 'Test notification - Vous avez été droppé de la matière "Mathématiques" le 15/01/2025 à 14:30. Vous devez reprendre ce cours l\'année prochaine.',
                'type' => 'warning'
            ]);

            // Associer à l'utilisateur
            $notification->utilisateurs()->attach($user->id, ['lu_a' => false]);

            $this->info("✅ Notification de test créée avec succès!");
            $this->info("ID de la notification: {$notification->id}");
            $this->info("Message: {$notification->message}");
            $this->info("Type: {$notification->type}");

            // Afficher les informations de connexion
            $this->info("\n📋 Pour tester les notifications:");
            $this->info("1. Connectez-vous avec l'email: {$user->email}");
            $this->info("2. Mot de passe: password");
            $this->info("3. Les notifications devraient s'afficher automatiquement");

        } else {
            $this->error("❌ Aucun utilisateur dropped trouvé");
            $this->info("Exécutez d'abord: php artisan db:seed --class=CreateDroppedStudentsSeeder");
        }

        $this->info("\n🎉 Test terminé!");
    }
}
