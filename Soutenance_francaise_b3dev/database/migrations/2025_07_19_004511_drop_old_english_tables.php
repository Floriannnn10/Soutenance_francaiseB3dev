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
        // Désactiver les contraintes de clés étrangères temporairement
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Supprimer les anciennes tables en anglais qui font doublon

        // Vérifier et supprimer la table academic_years si elle existe
        if (Schema::hasTable('academic_years')) {
            Schema::dropIfExists('academic_years');
        }

        // Vérifier et supprimer la table semesters si elle existe
        if (Schema::hasTable('semesters')) {
            Schema::dropIfExists('semesters');
        }

        // Autres tables en anglais qui pourraient faire doublon
        if (Schema::hasTable('presence_statuses')) {
            Schema::dropIfExists('presence_statuses');
        }

        if (Schema::hasTable('session_statuses')) {
            Schema::dropIfExists('session_statuses');
        }

        if (Schema::hasTable('notification_types')) {
            Schema::dropIfExists('notification_types');
        }

        // Réactiver les contraintes de clés étrangères
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Note: On ne recrée pas les anciennes tables car elles ne sont plus nécessaires
        // Cette migration est irréversible par design
    }
};
