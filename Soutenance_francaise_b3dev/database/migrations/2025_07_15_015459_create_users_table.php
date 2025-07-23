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
        Schema::create('users', function (Blueprint $table) {
            $table->integer('id')->nullable(false)->primary()->autoIncrement();
            $table->string('nom')->nullable(false);
            $table->string('prenom')->nullable(false);
            $table->string('email')->nullable(false);
            $table->timestamp('email_verified_at');
            $table->string('password')->nullable(false);
            $table->string('photo');
            $table->integer('role_id')->nullable(false);
            $table->string('remember_token');
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
            $table->timestamp('last_login_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');

    }
};
