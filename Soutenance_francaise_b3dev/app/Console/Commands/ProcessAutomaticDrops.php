<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DropNotificationService;

class ProcessAutomaticDrops extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'drops:process-automatic {--check-notifications : Vérifier et envoyer les notifications manquantes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Traiter automatiquement les drops basés sur le taux de présence des étudiants';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Début du traitement automatique des drops...');

        $service = new DropNotificationService();

        if ($this->option('check-notifications')) {
            $this->info('📧 Vérification et envoi des notifications manquantes...');
            $service->checkAndSendMissingNotifications();
            $this->info('✅ Notifications manquantes traitées avec succès !');
        } else {
            $this->info('📊 Analyse des taux de présence et création des drops automatiques...');
            $service->processAutomaticDrops();
            $this->info('✅ Traitement automatique des drops terminé !');
        }

        $this->info('🎉 Opération terminée avec succès !');
    }
}
