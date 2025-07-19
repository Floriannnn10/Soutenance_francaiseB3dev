<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Mettre à jour les semester_id dans course_sessions pour correspondre aux nouveaux IDs de la table semestres
        // Pour l'instant, nous allons simplement nous assurer que les données sont cohérentes

        // Si la table semesters existe encore, nous pouvons migrer les données
        if (Schema::hasTable('semesters')) {
            $oldSemesters = DB::table('semesters')->get();

            foreach ($oldSemesters as $oldSemester) {
                // Chercher le semestre correspondant dans la nouvelle table
                $newSemester = DB::table('semestres')
                    ->where('nom', 'like', '%' . $oldSemester->libelle . '%')
                    ->first();

                if ($newSemester) {
                    // Mettre à jour les références dans course_sessions
                    DB::table('course_sessions')
                        ->where('semester_id', $oldSemester->id)
                        ->update(['semester_id' => $newSemester->id]);
                }
            }
        } else {
            // Si la table semesters n'existe pas, nous devons nous assurer que les semester_id
            // dans course_sessions correspondent aux IDs de la table semestres

            // Pour l'instant, nous allons juste nettoyer les références invalides
            DB::table('course_sessions')
                ->whereNotIn('semester_id', function($query) {
                    $query->select('id')->from('semestres');
                })
                ->update(['semester_id' => null]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Pas de retour en arrière nécessaire pour cette migration de données
    }
};
