<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exercise_videos', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('category')->index(); // e.g. Chest, Back, Legs
            $table->string('url', 500);          // YouTube, Vimeo or a direct .mp4 link
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('training_plans', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            // Audience: one client, everyone on a package, or (both null) every client.
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('program_id')->nullable()->constrained()->nullOnDelete();
            $table->json('days'); // [{ name, exercises: [{ name, sets, reps, rest, note, video_id }] }]
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_plans');
        Schema::dropIfExists('exercise_videos');
    }
};
