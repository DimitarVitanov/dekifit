<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nutrition_plans', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            // Audience: one client, everyone on a package, or (both null) every client.
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('program_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedSmallInteger('calories')->nullable();
            $table->unsignedSmallInteger('protein_g')->nullable();
            $table->unsignedSmallInteger('carbs_g')->nullable();
            $table->unsignedSmallInteger('fat_g')->nullable();
            $table->json('meals'); // [{ name, time, note, items: [{ food, amount }] }]
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nutrition_plans');
    }
};
