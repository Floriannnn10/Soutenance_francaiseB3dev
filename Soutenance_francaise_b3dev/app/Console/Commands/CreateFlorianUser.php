<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Role;
use App\Models\Enseignant;
use App\Models\Matiere;
use Illuminate\Support\Facades\Hash;

class CreateFlorianUser extends Command
{
    protected $signature = 'create:florian';
    protected $description = 'Créer l\'utilisateur Florian avec un profil enseignant';

    public function handle()
    {
        // Vérifier si l'utilisateur existe déjà
        $existingUser = User::where('email', 'florian@ifran.ci')->first();

        if ($existingUser) {
            $this->info('L\'utilisateur florian@ifran.ci existe déjà.');
            return;
        }

        $enseignantRole = Role::where('code', 'enseignant')->first();

        if (!$enseignantRole) {
            $this->error('Le rôle enseignant n\'existe pas.');
            return;
        }

        // Créer l'utilisateur
        $user = User::create([
            'nom' => 'Banga',
            'prenom' => 'Florian',
            'email' => 'florian@ifran.ci',
            'password' => Hash::make('password'),
        ]);

        // Attacher le rôle enseignant
        $user->roles()->attach($enseignantRole->id);

        // Créer le profil enseignant
        $enseignant = Enseignant::create([
            'user_id' => $user->id,
            'nom' => 'Banga',
            'prenom' => 'Florian',
        ]);

        // Attribuer quelques matières à l'enseignant
        $matieres = Matiere::take(3)->get();
        if ($matieres->isNotEmpty()) {
            $enseignant->matieres()->attach($matieres->pluck('id'));
        }

        $this->info('Utilisateur enseignant florian@ifran.ci créé avec succès.');
        $this->info('Email: florian@ifran.ci');
        $this->info('Mot de passe: password');
    }
}
