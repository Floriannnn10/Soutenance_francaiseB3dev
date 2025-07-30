?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Enseignant;
use App\Models\Role;

class CreateFlorianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Vérifier si l'enseignant existe déjà
        $enseignant = Enseignant::where('email', 'florian@ifran.com')->first();
        if ($enseignant) {
            $this->command->info('L\'enseignant Florian existe déjà.');
            return;
        }

        // Créer l'utilisateur Florian
        $user = User::create([
            'nom' => 'Georges Emmanuel',
            'prenom' => 'Florian',
            'email' => 'florian@ifran.com',
            'password' => bcrypt('password'),
        ]);

        // Attacher le rôle enseignant
        $roleEnseignant = Role::where('code', 'enseignant')->first();
        if ($roleEnseignant) {
            $user->roles()->attach($roleEnseignant->id);
            $this->command->info('Rôle enseignant attaché');
        }

        // Créer le profil enseignant
        $enseignant = Enseignant::create([
            'user_id' => $user->id,
            'nom' => 'Georges Emmanuel',
            'prenom' => 'Florian',
            'email' => 'florian@ifran.com',
        ]);

        $this->command->info('Enseignant Florian créé avec succès:');
        $this->command->info("   - Nom: {$enseignant->nom} {$enseignant->prenom}");
        $this->command->info("   - Email: {$enseignant->email}");
        $this->command->info("   - ID: {$enseignant->id}");
        $this->command->info("   - User ID: {$user->id}");
        $this->command->info('');
        $this->command->info('Informations de connexion:');
        $this->command->info('Email: florian@ifran.com');
        $this->command->info('Mot de passe: password');
    }
}
