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
        Schema::create('course_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classe_id')->constrained();
            $table->foreignId('matiere_id')->constrained();
            $table->foreignId('enseignant_id')->constrained();
            $table->foreignId('type_cours_id')->constrained('types_cours');
            $table->foreignId('status_id')->constrained('statuts_session');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->string('location')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('replacement_for_session_id')->nullable()->constrained('course_sessions');
            $table->foreignId('annee_academique_id')->constrained('annees_academiques');
            $table->foreignId('semester_id')->constrained('semestres');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_sessions');
    }
};
