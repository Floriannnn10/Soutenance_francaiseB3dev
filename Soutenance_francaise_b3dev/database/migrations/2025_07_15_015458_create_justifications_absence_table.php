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
        Schema::create('justifications_absence', function (Blueprint $table) {
            $table->id();
            $table->foreignId('justifie_par_user_id')->constrained('users');
            $table->date('date_justification');
            $table->text('motif');
            $table->foreignId('presence_id')->constrained('presences');
            $table->string('piece_jointe')->nullable();
            $table->enum('statut', ['En attente', 'Approuvée', 'Rejetée'])->default('En attente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('justifications_absence');
    }
};
