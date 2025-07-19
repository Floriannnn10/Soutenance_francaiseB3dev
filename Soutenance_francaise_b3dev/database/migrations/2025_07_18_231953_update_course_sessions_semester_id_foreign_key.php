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
        Schema::table('course_sessions', function (Blueprint $table) {
            // Supprimer la contrainte existante si elle existe
            $table->dropForeign(['semester_id']);

            // Modifier la colonne pour qu'elle fasse référence à la nouvelle table semestres
            $table->foreign('semester_id')->references('id')->on('semestres')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_sessions', function (Blueprint $table) {
            $table->dropForeign(['semester_id']);
        });
    }
};
