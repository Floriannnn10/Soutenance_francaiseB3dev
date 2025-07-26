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
        Schema::create('sessions_de_cours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classe_id')->constrained();
            $table->foreignId('matiere_id')->constrained();
            $table->foreignId('enseignant_id')->constrained();
            $table->foreignId('type_cours_id')->constrained('types_cours');
            $table->foreignId('annee_academique_id')->constrained('annees_academiques');
            $table->foreignId('semestre_id')->constrained('semestres');
            $table->string('jour');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->string('statut')->default('planifié');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions_de_cours');
    }
};
