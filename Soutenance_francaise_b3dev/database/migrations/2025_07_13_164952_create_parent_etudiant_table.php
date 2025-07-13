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
        Schema::create('parent_etudiant', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->constrained()->onDelete('cascade');
            $table->foreignId('etudiant_id')->constrained()->onDelete('cascade');
            $table->enum('type_relation', ['pere', 'mere', 'tuteur', 'autre']);
            $table->boolean('est_responsable_legal')->default(false);
            $table->boolean('peut_recevoir_notifications')->default(true);
            $table->timestamps();

            $table->unique(['parent_id', 'etudiant_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parent_etudiant');
    }
};
