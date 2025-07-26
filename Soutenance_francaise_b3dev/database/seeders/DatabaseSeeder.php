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
            StatutsPresenceSeeder::class,
            StatutsSessionSeeder::class,
            TypesCoursSeeder::class,
            AdminSeeder::class,
            AnneesAcademiquesSeeder::class,
            SemestresSeeder::class,
            PromotionsSeeder::class,
            ClassesSeeder::class,
            MatieresSeeder::class,
            EnseignantsSeeder::class,
            EtudiantsSeeder::class,
            ParentsSeeder::class,
            CoordinateursSeeder::class,
        ]);
    }
}
