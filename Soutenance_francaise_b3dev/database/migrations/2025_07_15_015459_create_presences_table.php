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
            $table->integer('id')->nullable(false)->primary()->autoIncrement();
            $table->integer('etudiant_id')->nullable(false);
            $table->integer('course_session_id')->nullable(false);
            $table->integer('presence_status_id')->nullable(false);
            $table->timestamp('enregistre_le');
            $table->integer('enregistre_par_user_id');
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
        Schema::dropIfExists('presences');

    }
};