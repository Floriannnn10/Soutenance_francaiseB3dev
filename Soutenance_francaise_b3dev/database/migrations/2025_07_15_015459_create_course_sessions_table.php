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
            $table->integer('id')->nullable(false)->primary()->autoIncrement();
            $table->integer('classe_id')->nullable(false);
            $table->integer('matiere_id')->nullable(false);
            $table->integer('enseignant_id')->nullable(false);
            $table->integer('type_cours_id')->nullable(false);
            $table->integer('status_id')->nullable(false);
            $table->datetime('start_time')->nullable(false);
            $table->datetime('end_time')->nullable(false);
            $table->string('location');
            $table->text('notes');
            $table->integer('replacement_for_session_id');
            $table->integer('academic_year_id');
            $table->integer('semester_id');
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
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