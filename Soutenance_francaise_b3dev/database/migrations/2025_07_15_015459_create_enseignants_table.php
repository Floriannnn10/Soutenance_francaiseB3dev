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
        Schema::create('enseignants', function (Blueprint $table) {
            $table->integer('id')->nullable(false)->primary()->autoIncrement();
            $table->integer('user_id')->nullable(false);
            $table->string('prenom')->nullable(false);
            $table->string('nom')->nullable(false);
            $table->string('email')->unique()->nullable(false);
            $table->string('photo');
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enseignants');

    }
};
