<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * The "Start" package is now called "Basic". The slug stays "start" so existing
 * links and checkout URLs keep working; past orders keep the name they were placed under.
 */
return new class extends Migration
{
    public function up(): void
    {
        $this->rename('Start', 'Basic');
    }

    public function down(): void
    {
        $this->rename('Basic', 'Start');
    }

    private function rename(string $from, string $to): void
    {
        DB::table('programs')->where('slug', 'start')->where('title', $from)->update(['title' => $to]);

        // "Everything in Start, plus:" on the packages above it.
        foreach (DB::table('programs')->get(['id', 'content', 'features']) as $program) {
            DB::table('programs')->where('id', $program->id)->update([
                'content' => $program->content ? str_replace("everything in {$from}", "everything in {$to}", $program->content) : $program->content,
                'features' => $program->features ? str_replace("Everything in {$from},", "Everything in {$to},", $program->features) : $program->features,
            ]);
        }
    }
};
