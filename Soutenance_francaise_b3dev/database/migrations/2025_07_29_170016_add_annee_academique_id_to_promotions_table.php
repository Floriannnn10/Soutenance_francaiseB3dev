<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get the first annee_academique or create one
        $anneeAcademique = DB::table('annees_academiques')->first();
        if (!$anneeAcademique) {
            $anneeAcademique = DB::table('annees_academiques')->insertGetId([
                'nom' => '2024-2025',
                'date_debut' => '2024-09-01',
                'date_fin' => '2025-08-31',
                'est_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $anneeAcademique = $anneeAcademique->id;
        }

        // Update existing promotions with the annee_academique_id
        DB::table('promotions')->whereNull('annee_academique_id')->update(['annee_academique_id' => $anneeAcademique]);

        // Check if the column already exists
        if (!Schema::hasColumn('promotions', 'annee_academique_id')) {
            Schema::table('promotions', function (Blueprint $table) {
                $table->foreignId('annee_academique_id')->nullable()->constrained('annees_academiques')->onDelete('cascade');
            });
        } else {
            // Column exists, just add the foreign key constraint
            Schema::table('promotions', function (Blueprint $table) {
                $table->foreign('annee_academique_id')->references('id')->on('annees_academiques')->onDelete('cascade');
            });
        }

        // Make the column not nullable
        Schema::table('promotions', function (Blueprint $table) {
            $table->foreignId('annee_academique_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('promotions', function (Blueprint $table) {
            $table->dropForeign(['annee_academique_id']);
            $table->dropColumn('annee_academique_id');
        });
    }
};
