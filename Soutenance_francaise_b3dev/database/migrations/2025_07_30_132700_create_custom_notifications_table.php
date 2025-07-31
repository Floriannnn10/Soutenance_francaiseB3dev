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
        Schema::create('custom_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('message');
            $table->string('type')->default('warning');
            $table->timestamps();
        });

        // Table pivot pour associer les notifications aux utilisateurs
        Schema::create('custom_notification_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('custom_notification_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->boolean('lu_a')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_notification_user');
        Schema::dropIfExists('custom_notifications');
    }
};
