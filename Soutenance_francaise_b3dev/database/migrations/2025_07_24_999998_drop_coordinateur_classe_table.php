<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('coordinateur_classe');
    }

    public function down(): void
    {
        Schema::create('coordinateur_classe', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('coordinateur_id');
            $table->unsignedBigInteger('classe_id');
            $table->timestamps();
        });
    }
};
