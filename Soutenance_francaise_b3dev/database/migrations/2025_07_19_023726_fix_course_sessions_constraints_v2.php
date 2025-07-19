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
        // Désactiver les contraintes temporairement
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Supprimer les anciennes contraintes avec SQL direct (si elles existent)
        DB::statement('ALTER TABLE course_sessions DROP FOREIGN KEY IF EXISTS course_sessions_academic_year_id_foreign');

        // Ajouter les nouvelles contraintes
        DB::statement('ALTER TABLE course_sessions ADD CONSTRAINT course_sessions_academic_year_id_foreign FOREIGN KEY (academic_year_id) REFERENCES annees_academiques(id) ON DELETE SET NULL');
        DB::statement('ALTER TABLE course_sessions ADD CONSTRAINT course_sessions_status_id_foreign FOREIGN KEY (status_id) REFERENCES statuts_session(id) ON DELETE RESTRICT');

        // Réactiver les contraintes
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Désactiver les contraintes temporairement
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Supprimer les contraintes ajoutées
        DB::statement('ALTER TABLE course_sessions DROP FOREIGN KEY IF EXISTS course_sessions_academic_year_id_foreign');
        DB::statement('ALTER TABLE course_sessions DROP FOREIGN KEY IF EXISTS course_sessions_status_id_foreign');

        // Réactiver les contraintes
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
};
