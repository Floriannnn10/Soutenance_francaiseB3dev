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
        Schema::create('classes', function (Blueprint $table) {
            $table->integer('id')->nullable(false)->primary()->autoIncrement();
            $table->string('nom')->nullable(false);
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
            $table->string('niveau');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classes');

    }
};