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
        Schema::create('coordinateur_classe', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coordinateur_id')->constrained()->onDelete('cascade');
            $table->foreignId('classe_id')->constrained()->onDelete('cascade');
            $table->foreignId('annee_academique_id')->constrained('annees_academiques')->onDelete('cascade');
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
            $table->text('commentaire')->nullable();
            $table->boolean('est_actif')->default(true);
            $table->timestamps();

            $table->unique(['coordinateur_id', 'classe_id', 'annee_academique_id'], 'coord_classe_annee_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coordinateur_classe');
    }
};
