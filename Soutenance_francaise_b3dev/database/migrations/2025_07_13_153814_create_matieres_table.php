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
        Schema::create('matieres', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('code')->unique(); // Code unique de la matière
            $table->text('description')->nullable();
            $table->integer('coefficient')->default(1);
            $table->integer('heures_cm')->default(0); // Cours magistraux
            $table->integer('heures_td')->default(0); // Travaux dirigés
            $table->integer('heures_tp')->default(0); // Travaux pratiques
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matieres');
    }
};
