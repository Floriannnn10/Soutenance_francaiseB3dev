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
        Schema::table('annees_academiques', function (Blueprint $table) {
            $table->boolean('est_actif')->default(true)->after('nom');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('annees_academiques', function (Blueprint $table) {
            $table->dropColumn('est_actif');
        });
    }
};
