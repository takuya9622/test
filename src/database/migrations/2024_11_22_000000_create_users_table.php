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
            $table->id(); // unsigned bigint primary key
            $table->string('name', 255)->notNullable();
            $table->string('email', 255)->unique()->notNullable();
            $table->string('password', 255)->notNullable();
            $table->string('profile_image', 500)->nullable(); // nullable since it's not specified as NOT NULL
            $table->string('postal_code', 255)->nullable();
            $table->string('address', 255)->nullable();
            $table->timestamps(); // created_at, updated_at
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
