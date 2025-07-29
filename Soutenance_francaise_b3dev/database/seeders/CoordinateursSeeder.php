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
                    'nom' => 'Bernard',
                    'prenom' => 'Sophie',
                    'email' => 'sophie.bernard@example.com',
                    'password' => bcrypt('password'),
                ],
                'coordinateur' => [
                    'prenom' => 'Sophie',
                    'nom' => 'Bernard',
                    'photo' => null
                ]
            ],
            [
                'user' => [
                    'nom' => 'Dubois',
                    'prenom' => 'Michel',
                    'email' => 'michel.dubois@example.com',
                    'password' => bcrypt('password'),
                ],
                'coordinateur' => [
                    'prenom' => 'Michel',
                    'nom' => 'Dubois',
                    'photo' => null
                ]
            ],
            [
                'user' => [
                    'nom' => 'Moreau',
                    'prenom' => 'Claire',
                    'email' => 'claire.moreau@example.com',
                    'password' => bcrypt('password'),
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
            $user->roles()->attach($roleCoordinateur->id);
            $coordinateur = new Coordinateur($data['coordinateur']);
            $coordinateur->email = $data['user']['email'];
            $coordinateur->user()->associate($user);
            $coordinateur->promotion()->associate($promotions[$index % $promotions->count()]);
            $coordinateur->save();
        }
    }
}
