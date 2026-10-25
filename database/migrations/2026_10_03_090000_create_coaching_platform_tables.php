<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->foreignId('program_id')->nullable()->after('is_admin')->constrained()->nullOnDelete();
            $table->boolean('must_change_password')->default(false)->after('program_id');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('program_id')->constrained()->nullOnDelete();
        });

        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('body');
            $table->boolean('is_pinned')->default(false);
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('progress_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('logged_on');
            $table->decimal('weight_kg', 5, 1);
            $table->decimal('body_fat_pct', 4, 1)->nullable();
            $table->decimal('waist_cm', 5, 1)->nullable();
            $table->string('note', 500)->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'logged_on']);
        });

        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('body')->nullable();
            $table->timestamps();
        });

        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('subject');
            $table->string('status')->default('open')->index(); // open (waiting for coach), answered, closed
            $table->timestamp('last_reply_at')->nullable();
            $table->timestamps();
        });

        Schema::create('ticket_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('body');
            $table->timestamps();
        });

        Schema::create('time_slots', function (Blueprint $table) {
            $table->id();
            $table->dateTime('starts_at')->unique();
            $table->dateTime('ends_at');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // the client who booked it
            $table->timestamp('booked_at')->nullable();
            $table->string('client_note', 500)->nullable();
            $table->string('meeting_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('time_slots');
        Schema::dropIfExists('ticket_messages');
        Schema::dropIfExists('tickets');
        Schema::dropIfExists('notes');
        Schema::dropIfExists('progress_entries');
        Schema::dropIfExists('announcements');

        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('program_id');
            $table->dropColumn(['phone', 'must_change_password']);
        });
    }
};
