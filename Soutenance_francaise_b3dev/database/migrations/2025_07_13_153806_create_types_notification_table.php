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
        Schema::create('types_notification', function (Blueprint $table) {
            $table->id();
            $table->string('nom'); // Droppé, Annulation de cours, Information
            $table->string('icone')->nullable(); // Icône pour l'affichage
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('types_notification');
    }
};
