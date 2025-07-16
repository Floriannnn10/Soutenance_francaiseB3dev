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
        $this->call([
            RolesSeeder::class,
            AdminSeeder::class,
            StatutsSeeder::class,
            AnneesAcademiquesSeeder::class,
            SemestresSeeder::class,
            ClassesSeeder::class,
            MatieresSeeder::class,
            TypesCoursSeeder::class,
            CoordinateursSeeder::class,
            ParentsSeeder::class,
            EtudiantsSeeder::class,
            EnseignantsSeeder::class,
            SessionsDeCoursSeeder::class,
            PresencesSeeder::class,
            NotificationsSeeder::class,
        ]);
    }
}
