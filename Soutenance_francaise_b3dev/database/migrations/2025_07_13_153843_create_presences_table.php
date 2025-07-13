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
        Schema::create('presences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('etudiant_id')->constrained('etudiants')->onDelete('cascade');
            $table->foreignId('session_de_cours_id')->constrained('sessions_de_cours')->onDelete('cascade');
            $table->foreignId('statut_presence_id')->constrained('statuts_presence')->onDelete('cascade');
            $table->foreignId('enregistre_par_utilisateur_id')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('est_justifiee')->default(false);
            $table->text('motif_justification')->nullable();
            $table->timestamp('enregistre_a');
            $table->timestamps();

            // Contrainte unique pour éviter les doublons
            $table->unique(['etudiant_id', 'session_de_cours_id'], 'unique_presence');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presences');
    }
};
