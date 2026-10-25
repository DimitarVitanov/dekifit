<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Body parts of the video library the coach has unlocked for this client, e.g. ["Chest", "Back"].
            $table->json('unlocked_video_categories')->nullable()->after('program_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('unlocked_video_categories');
        });
    }
};
