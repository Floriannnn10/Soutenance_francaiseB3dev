<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Added this import for DB facade

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('statuts_session', function (Blueprint $table) {
            $table->id();
            $table->string('nom')->unique();
            $table->string('description')->nullable();
            $table->string('couleur', 7)->default('#6B7280'); // Code couleur hexadécimal
            $table->timestamps();
        });

        // Insérer les statuts par défaut
        DB::table('statuts_session')->insert([
            ['nom' => 'Programmée', 'description' => 'Session programmée mais pas encore commencée', 'couleur' => '#3B82F6', 'created_at' => now(), 'updated_at' => now()],
            ['nom' => 'En cours', 'description' => 'Session en cours de réalisation', 'couleur' => '#10B981', 'created_at' => now(), 'updated_at' => now()],
            ['nom' => 'Terminée', 'description' => 'Session terminée avec succès', 'couleur' => '#059669', 'created_at' => now(), 'updated_at' => now()],
            ['nom' => 'Annulée', 'description' => 'Session annulée', 'couleur' => '#EF4444', 'created_at' => now(), 'updated_at' => now()],
            ['nom' => 'Reportée', 'description' => 'Session reportée à une date ultérieure', 'couleur' => '#F59E0B', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('statuts_session');
    }
};
