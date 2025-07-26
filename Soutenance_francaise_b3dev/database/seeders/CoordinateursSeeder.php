<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Coordinateur;
use App\Models\Promotion;

class CoordinateursSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roleCoordinateur = Role::where('code', 'coordinateur')->first();

        if (!$roleCoordinateur) {
            throw new \Exception('Le rôle coordinateur n\'existe pas. Veuillez exécuter le seeder RolesSeeder d\'abord.');
        }

        $promotions = Promotion::all();

        if ($promotions->isEmpty()) {
            throw new \Exception('Aucune promotion n\'existe. Veuillez exécuter le seeder PromotionsSeeder d\'abord.');
        }

        $coordinateurs = [
            [
                'user' => [
                    'name' => 'Sophie Bernard',
                    'email' => 'sophie.bernard@example.com',
                    'password' => bcrypt('password'),
                    'role_id' => $roleCoordinateur->id
                ],
                'coordinateur' => [
                    'prenom' => 'Sophie',
                    'nom' => 'Bernard',
                    'photo' => null
                ]
            ],
            [
                'user' => [
                    'name' => 'Michel Dubois',
                    'email' => 'michel.dubois@example.com',
                    'password' => bcrypt('password'),
                    'role_id' => $roleCoordinateur->id
                ],
                'coordinateur' => [
                    'prenom' => 'Michel',
                    'nom' => 'Dubois',
                    'photo' => null
                ]
            ],
            [
                'user' => [
                    'name' => 'Claire Moreau',
                    'email' => 'claire.moreau@example.com',
                    'password' => bcrypt('password'),
                    'role_id' => $roleCoordinateur->id
                ],
                'coordinateur' => [
                    'prenom' => 'Claire',
                    'nom' => 'Moreau',
                    'photo' => null
                ]
            ]
        ];

        foreach ($coordinateurs as $index => $data) {
            $user = User::create($data['user']);
            $coordinateur = new Coordinateur($data['coordinateur']);
            $coordinateur->user()->associate($user);
            $coordinateur->promotion()->associate($promotions[$index % $promotions->count()]);
            $coordinateur->save();
        }
    }
}
