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
        Schema::create('statuts_session', function (Blueprint $table) {
            $table->id();
            $table->string('nom'); // Prévue, Annulée, Reportée, Terminée
            $table->string('couleur')->default('#3B82F6'); // Couleur pour l'affichage
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('statuts_session');
    }
};
