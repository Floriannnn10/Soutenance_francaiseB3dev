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
            $table->foreignId('etudiant_id')->constrained();
            $table->foreignId('course_session_id')->constrained('course_sessions');
            $table->foreignId('statut_presence_id')->constrained('statuts_presence');
            $table->foreignId('enregistre_par_user_id')->constrained('users');
            $table->dateTime('enregistre_le');

            $table->timestamps();
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
