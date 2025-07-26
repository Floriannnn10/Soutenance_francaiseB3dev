<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Promotion;

class PromotionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $promotions = [
            [
                'nom' => 'B3 DEV',
                'description' => 'Bachelor 3 Développement'
            ],
            [
                'nom' => 'B3 CYBER',
                'description' => 'Bachelor 3 Cybersécurité'
            ],
            [
                'nom' => 'M1 DEV',
                'description' => 'Master 1 Développement'
            ],
            [
                'nom' => 'M1 CYBER',
                'description' => 'Master 1 Cybersécurité'
            ],
            [
                'nom' => 'M2 DEV',
                'description' => 'Master 2 Développement'
            ],
            [
                'nom' => 'M2 CYBER',
                'description' => 'Master 2 Cybersécurité'
            ]
        ];

        foreach ($promotions as $promotion) {
            Promotion::create($promotion);
        }
    }
}
