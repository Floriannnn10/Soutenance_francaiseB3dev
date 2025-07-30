<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Vérifier si la table existe
        if (Schema::hasTable('etudiant_matiere_dropped')) {
            // Supprimer la table existante
            Schema::dropIfExists('etudiant_matiere_dropped');
        }

        // Recréer la table avec la bonne structure
        Schema::create('etudiant_matiere_dropped', function (Blueprint $table) {
            $table->id();
            $table->foreignId('etudiant_id')->constrained('etudiants')->onDelete('cascade');
            $table->foreignId('matiere_id')->constrained('matieres')->onDelete('cascade');
            $table->foreignId('annee_academique_id')->constrained('annees_academiques')->onDelete('cascade');
            $table->foreignId('semestre_id')->constrained('semestres')->onDelete('cascade');
            $table->text('raison_drop')->nullable(); // Raison de l'abandon
            $table->date('date_drop'); // Date de l'abandon
            $table->foreignId('dropped_by')->nullable()->constrained('users')->onDelete('set null'); // Qui a marqué l'abandon
            $table->timestamps();

            // Index pour éviter les doublons
            $table->unique(['etudiant_id', 'matiere_id', 'annee_academique_id', 'semestre_id'], 'unique_etudiant_matiere_dropped');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('etudiant_matiere_dropped');
    }
};
