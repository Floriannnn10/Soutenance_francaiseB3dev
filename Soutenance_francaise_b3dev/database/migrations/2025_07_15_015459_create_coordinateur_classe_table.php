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
        Schema::create('coordinateur_classe', function (Blueprint $table) {
            $table->integer('id')->nullable(false)->primary()->autoIncrement();
            $table->integer('coordinateur_id')->nullable(false);
            $table->integer('classe_id')->nullable(false);
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coordinateur_classe');

    }
};