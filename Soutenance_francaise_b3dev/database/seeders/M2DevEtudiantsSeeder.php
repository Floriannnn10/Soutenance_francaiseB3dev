?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Etudiant;
use App\Models\Classe;
use App\Models\ParentEtudiant;
use Illuminate\Support\Facades\Hash;

class M2DevEtudiantsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roleEtudiant = Role::where('code', 'etudiant')->first();
        $roleParent = Role::where('code', 'parent')->first();

        if (!$roleEtudiant) {
            throw new \Exception('Le rôle étudiant n\'existe pas. Veuillez exécuter le seeder RolesSeeder d\'abord.');
        }

        if (!$roleParent) {
            throw new \Exception('Le rôle parent n\'existe pas. Veuillez exécuter le seeder RolesSeeder d\'abord.');
        }

        // Récupérer les classes M2 DEV
        $classeM2DevA = Classe::where('nom', 'M2 DEV A')->first();
        $classeM2DevB = Classe::where('nom', 'M2 DEV B')->first();

        if (!$classeM2DevA || !$classeM2DevB) {
            throw new \Exception('Les classes M2 DEV A et M2 DEV B n\'existent pas. Veuillez exécuter le seeder ClassesSeeder d\'abord.');
        }

        // Données des étudiants pour M2 DEV A
        $etudiantsM2DevA = [
            ['nom' => 'Konan', 'prenom' => 'Miyah', 'email' => 'miyah.konan.m2@ifran.ci'],
            ['nom' => 'Bamba', 'prenom' => 'Aissatou', 'email' => 'aissatou.bamba.m2@ifran.ci'],
            ['nom' => 'Kouassi', 'prenom' => 'Fatou', 'email' => 'fatou.kouassi.m2@ifran.ci'],
            ['nom' => 'Traore', 'prenom' => 'Moussa', 'email' => 'moussa.traore.m2@ifran.ci'],
            ['nom' => 'Diabate', 'prenom' => 'Aminata', 'email' => 'aminata.diabate.m2@ifran.ci'],
            ['nom' => 'Ouattara', 'prenom' => 'Kadidja', 'email' => 'kadidja.ouattara.m2@ifran.ci'],
            ['nom' => 'Yao', 'prenom' => 'Kouassi', 'email' => 'kouassi.yao.m2@ifran.ci'],
            ['nom' => 'Kone', 'prenom' => 'Fatima', 'email' => 'fatima.kone.m2@ifran.ci'],
            ['nom' => 'Diallo', 'prenom' => 'Mariam', 'email' => 'mariam.diallo.m2@ifran.ci'],
            ['nom' => 'Coulibaly', 'prenom' => 'Sekou', 'email' => 'sekou.coulibaly.m2@ifran.ci'],
        ];

        // Données des étudiants pour M2 DEV B
        $etudiantsM2DevB = [
            ['nom' => 'Bamba', 'prenom' => 'Issouf', 'email' => 'issouf.bamba.m2@ifran.ci'],
            ['nom' => 'Kouassi', 'prenom' => 'Adama', 'email' => 'adama.kouassi.m2@ifran.ci'],
            ['nom' => 'Traore', 'prenom' => 'Bakary', 'email' => 'bakary.traore.m2@ifran.ci'],
            ['nom' => 'Diabate', 'prenom' => 'Fanta', 'email' => 'fanta.diabate.m2@ifran.ci'],
            ['nom' => 'Ouattara', 'prenom' => 'Mamadou', 'email' => 'mamadou.ouattara.m2@ifran.ci'],
            ['nom' => 'Yao', 'prenom' => 'Kouadio', 'email' => 'kouadio.yao.m2@ifran.ci'],
            ['nom' => 'Kone', 'prenom' => 'Aicha', 'email' => 'aicha.kone.m2@ifran.ci'],
            ['nom' => 'Diallo', 'prenom' => 'Ousmane', 'email' => 'ousmane.diallo.m2@ifran.ci'],
            ['nom' => 'Coulibaly', 'prenom' => 'Fatou', 'email' => 'fatou.coulibaly.m2@ifran.ci'],
            ['nom' => 'Konan', 'prenom' => 'Yaya', 'email' => 'yaya.konan.m2@ifran.ci'],
        ];

        $this->createEtudiantsForClasse($etudiantsM2DevA, $classeM2DevA, $roleEtudiant, $roleParent, 'M2 DEV A');
        $this->createEtudiantsForClasse($etudiantsM2DevB, $classeM2DevB, $roleEtudiant, $roleParent, 'M2 DEV B');

        $this->command->info('🎉 20 étudiants M2 DEV ont été créés avec succès !');
        $this->command->info('📚 M2 DEV A : 10 étudiants');
        $this->command->info('📚 M2 DEV B : 10 étudiants');
        $this->command->info('👨‍👩‍👧‍👦 20 parents ont été créés et associés aux étudiants');
    }

    private function createEtudiantsForClasse($etudiants, $classe, $roleEtudiant, $roleParent, $classeNom)
    {
        $this->command->info("Création des étudiants pour {$classeNom}...");

        foreach ($etudiants as $etudiantData) {
            // Créer l'utilisateur étudiant
            $userEtudiant = User::create([
                'nom' => $etudiantData['nom'],
                'prenom' => $etudiantData['prenom'],
                'email' => $etudiantData['email'],
                'password' => Hash::make('password'),
            ]);

            $userEtudiant->roles()->attach($roleEtudiant->id);

            // Créer l'étudiant
            $etudiant = Etudiant::create([
                'user_id' => $userEtudiant->id,
                'classe_id' => $classe->id,
                'nom' => $etudiantData['nom'],
                'prenom' => $etudiantData['prenom'],
                'email' => $etudiantData['email'],
                'password' => Hash::make('password'),
                'date_naissance' => now()->subYears(rand(22, 26))->format('Y-m-d'),
                'photo' => null
            ]);

            // Créer l'utilisateur parent
            $userParent = User::create([
                'nom' => $etudiantData['nom'],
                'prenom' => "Parent de {$etudiantData['prenom']}",
                'email' => "parent.{$etudiantData['email']}",
                'password' => Hash::make('password'),
            ]);

            $userParent->roles()->attach($roleParent->id);

            // Créer le parent
            $parent = ParentEtudiant::create([
                'user_id' => $userParent->id,
                'nom' => $etudiantData['nom'],
                'prenom' => "Parent de {$etudiantData['prenom']}",
                'telephone' => '+22507' . str_pad(rand(10000000, 99999999), 8, '0'),
                'profession' => 'Professionnel',
                'adresse' => 'Abidjan, Côte d\'Ivoire',
                'photo' => null
            ]);

            // Associer le parent à l'étudiant
            $parent->etudiants()->attach($etudiant->id);

            $this->command->info("✅ Étudiant créé: {$etudiantData['prenom']} {$etudiantData['nom']} ({$classeNom})");
        }
    }
}
