<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Exécuter les seeders dans l'ordre pour respecter les contraintes de clés étrangères
        $this->call([
            RolesSeeder::class,
            StatutsSeeder::class,
            AnneesAcademiquesSeeder::class,
            ClassesSeeder::class,
            MatieresSeeder::class,
            TypesCoursSeeder::class,
            EtudiantsSeeder::class,
            EnseignantsSeeder::class,
            SessionsDeCoursSeeder::class,
            PresencesSeeder::class,
            NotificationsSeeder::class,
        ]);
    }
}
