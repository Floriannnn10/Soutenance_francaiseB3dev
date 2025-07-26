<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Classe;
use App\Models\Promotion;

class ClassesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $promotions = Promotion::all();

        if ($promotions->isEmpty()) {
            throw new \Exception('Aucune promotion n\'existe. Veuillez exécuter le seeder PromotionsSeeder d\'abord.');
        }

        foreach ($promotions as $promotion) {
            $classes = [
                [
                    'nom' => $promotion->nom . ' A',
                    'promotion_id' => $promotion->id
                ],
                [
                    'nom' => $promotion->nom . ' B',
                    'promotion_id' => $promotion->id
                ]
            ];

            foreach ($classes as $classe) {
                Classe::create($classe);
            }
        }
    }
}
