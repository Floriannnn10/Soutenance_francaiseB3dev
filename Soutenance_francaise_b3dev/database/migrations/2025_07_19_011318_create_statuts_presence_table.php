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
        Schema::create('statuts_presence', function (Blueprint $table) {
            $table->id();
            $table->string('nom')->unique();
            $table->string('description')->nullable();
            $table->string('couleur', 7)->default('#6B7280'); // Code couleur hexadécimal
            $table->timestamps();
        });

        // Insérer les statuts par défaut
        DB::table('statuts_presence')->insert([
            ['nom' => 'Présent', 'description' => 'Étudiant présent à la session', 'couleur' => '#10B981', 'created_at' => now(), 'updated_at' => now()],
            ['nom' => 'Absent', 'description' => 'Étudiant absent sans justification', 'couleur' => '#EF4444', 'created_at' => now(), 'updated_at' => now()],
            ['nom' => 'Absent Justifié', 'description' => 'Étudiant absent avec justification valide', 'couleur' => '#F59E0B', 'created_at' => now(), 'updated_at' => now()],
            ['nom' => 'Retard', 'description' => 'Étudiant arrivé en retard', 'couleur' => '#8B5CF6', 'created_at' => now(), 'updated_at' => now()],
            ['nom' => 'Parti Tôt', 'description' => 'Étudiant parti avant la fin de la session', 'couleur' => '#EC4899', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('statuts_presence');
    }
};
