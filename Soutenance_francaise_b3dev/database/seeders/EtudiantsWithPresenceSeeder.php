<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Etudiant;
use App\Models\Classe;
use App\Models\SessionDeCours;
use App\Models\Presence;
use Carbon\Carbon;

class EtudiantsWithPresenceSeeder extends Seeder
{
    public function run(): void
    {
        // Nettoyer seulement les présences existantes
        DB::table('presences')->delete();

        // Récupérer les statuts de présence
        $statutsPresence = DB::table('statuts_presence')->get();
        $present = $statutsPresence->where('nom', 'Présent')->first();
        $absent = $statutsPresence->where('nom', 'Absent')->first();
        $justifie = $statutsPresence->where('nom', 'Justifié')->first();
        $retard = $statutsPresence->where('nom', 'Retard')->first();

        // Récupérer les classes
        $classes = Classe::with('promotion')->get();

        foreach ($classes as $classe) {
            $this->createStudentsForClass($classe, $present, $absent, $justifie, $retard);
        }

        echo "✅ Étudiants créés avec succès pour toutes les classes !\n";
    }

    private function createStudentsForClass($classe, $present, $absent, $justifie, $retard)
    {
        $studentsData = [
            'B3 DEV A' => [
                ['nom' => 'Konan', 'prenom' => 'Miyah', 'email' => 'miyah.konan@ifran.ci'],
                ['nom' => 'Bamba', 'prenom' => 'Aissatou', 'email' => 'aissatou.bamba@ifran.ci'],
                ['nom' => 'Kouassi', 'prenom' => 'Fatou', 'email' => 'fatou.kouassi@ifran.ci'],
                ['nom' => 'Traore', 'prenom' => 'Moussa', 'email' => 'moussa.traore@ifran.ci'],
                ['nom' => 'Diabate', 'prenom' => 'Aminata', 'email' => 'aminata.diabate@ifran.ci'],
                ['nom' => 'Ouattara', 'prenom' => 'Kader', 'email' => 'kader.ouattara@ifran.ci'],
                ['nom' => 'Coulibaly', 'prenom' => 'Salimata', 'email' => 'salimata.coulibaly@ifran.ci'],
            ],
            'B3 DEV B' => [
                ['nom' => 'Yao', 'prenom' => 'Kouassi', 'email' => 'kouassi.yao@ifran.ci'],
                ['nom' => 'Kone', 'prenom' => 'Mariam', 'email' => 'mariam.kone@ifran.ci'],
                ['nom' => 'Soro', 'prenom' => 'Issouf', 'email' => 'issouf.soro@ifran.ci'],
                ['nom' => 'Toure', 'prenom' => 'Aicha', 'email' => 'aicha.toure@ifran.ci'],
                ['nom' => 'Kouame', 'prenom' => 'Yves', 'email' => 'yves.kouame@ifran.ci'],
                ['nom' => 'N\'Guessan', 'prenom' => 'Fatim', 'email' => 'fatim.nguessan@ifran.ci'],
                ['nom' => 'Bailly', 'prenom' => 'Christian', 'email' => 'christian.bailly@ifran.ci'],
            ],
            'B3 DEV C' => [
                ['nom' => 'Adou', 'prenom' => 'Georges', 'email' => 'georges.adou@ifran.ci'],
                ['nom' => 'Emmanuel', 'prenom' => 'Florian', 'email' => 'florian.emmanuel@ifran.ci'],
                ['nom' => 'Banga', 'prenom' => 'Adou', 'email' => 'adou.banga@ifran.ci'],
                ['nom' => 'Kouassi', 'prenom' => 'Marie', 'email' => 'marie.kouassi@ifran.ci'],
                ['nom' => 'Traore', 'prenom' => 'Ibrahim', 'email' => 'ibrahim.traore@ifran.ci'],
                ['nom' => 'Diabate', 'prenom' => 'Hawa', 'email' => 'hawa.diabate@ifran.ci'],
                ['nom' => 'Ouattara', 'prenom' => 'Mamadou', 'email' => 'mamadou.ouattara@ifran.ci'],
            ],
            'M2 DEV A' => [
                ['nom' => 'Kouame', 'prenom' => 'Sarah', 'email' => 'sarah.kouame@ifran.ci'],
                ['nom' => 'Diallo', 'prenom' => 'Fatoumata', 'email' => 'fatoumata.diallo@ifran.ci'],
                ['nom' => 'Keita', 'prenom' => 'Moussa', 'email' => 'moussa.keita@ifran.ci'],
                ['nom' => 'Camara', 'prenom' => 'Aissatou', 'email' => 'aissatou.camara@ifran.ci'],
                ['nom' => 'Sangare', 'prenom' => 'Kouassi', 'email' => 'kouassi.sangare@ifran.ci'],
                ['nom' => 'Konate', 'prenom' => 'Mariam', 'email' => 'mariam.konate@ifran.ci'],
                ['nom' => 'Fofana', 'prenom' => 'Issouf', 'email' => 'issouf.fofana@ifran.ci'],
            ],
            'M2 DEV B' => [
                ['nom' => 'Toure', 'prenom' => 'Aicha', 'email' => 'aicha.toure.m2@ifran.ci'],
                ['nom' => 'Cisse', 'prenom' => 'Yves', 'email' => 'yves.cisse@ifran.ci'],
                ['nom' => 'Bamba', 'prenom' => 'Fatim', 'email' => 'fatim.bamba@ifran.ci'],
                ['nom' => 'Kone', 'prenom' => 'Christian', 'email' => 'christian.kone@ifran.ci'],
                ['nom' => 'Traore', 'prenom' => 'Mariam', 'email' => 'mariam.traore@ifran.ci'],
                ['nom' => 'Diabate', 'prenom' => 'Kouassi', 'email' => 'kouassi.diabate@ifran.ci'],
                ['nom' => 'Ouattara', 'prenom' => 'Issouf', 'email' => 'issouf.ouattara@ifran.ci'],
            ],
        ];

        $studentsForClass = $studentsData[$classe->nom] ?? [
            ['nom' => 'Etudiant', 'prenom' => 'Test', 'email' => 'etudiant.test.' . $classe->id . '@ifran.ci'],
        ];

        foreach ($studentsForClass as $studentData) {
            // Vérifier si l'utilisateur existe déjà
            $existingUser = User::where('email', $studentData['email'])->first();

            if (!$existingUser) {
                // Créer l'utilisateur
                $user = User::create([
                    'nom' => $studentData['nom'],
                    'prenom' => $studentData['prenom'],
                    'email' => $studentData['email'],
                    'password' => Hash::make('password'),
                    'photo' => null,
                ]);

                // Assigner le rôle étudiant
                $roleEtudiant = DB::table('roles')->where('nom', 'Étudiant')->first();
                DB::table('role_user')->insert([
                    'user_id' => $user->id,
                    'role_id' => $roleEtudiant->id,
                ]);

                // Créer l'étudiant
                $etudiant = Etudiant::create([
                    'user_id' => $user->id,
                    'nom' => $studentData['nom'],
                    'prenom' => $studentData['prenom'],
                    'classe_id' => $classe->id,
                    'email' => $studentData['email'],
                    'password' => Hash::make('password'),
                    'date_naissance' => Carbon::now()->subYears(20)->subDays(rand(0, 365)),
                    'photo' => null,
                ]);

                echo "✅ Étudiant créé : {$studentData['prenom']} {$studentData['nom']} dans {$classe->nom}\n";
            } else {
                // Vérifier si l'étudiant existe déjà pour cette classe
                $existingEtudiant = Etudiant::where('user_id', $existingUser->id)
                    ->where('classe_id', $classe->id)
                    ->first();

                if (!$existingEtudiant) {
                    // Créer seulement l'étudiant pour cette classe
                    $etudiant = Etudiant::create([
                        'user_id' => $existingUser->id,
                        'nom' => $studentData['nom'],
                        'prenom' => $studentData['prenom'],
                        'classe_id' => $classe->id,
                        'email' => $studentData['email'],
                        'password' => Hash::make('password'),
                        'date_naissance' => Carbon::now()->subYears(20)->subDays(rand(0, 365)),
                        'photo' => null,
                    ]);

                    echo "✅ Étudiant ajouté à la classe : {$studentData['prenom']} {$studentData['nom']} dans {$classe->nom}\n";
                } else {
                    echo "ℹ️ Étudiant déjà présent : {$studentData['prenom']} {$studentData['nom']} dans {$classe->nom}\n";
                }
            }
        }

        echo "✅ {$classe->nom} : " . count($studentsForClass) . " étudiants traités\n";
    }
}
