<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained()->onDelete('cascade');
            $table->string('key');
            $table->string('name');
            $table->string('eyebrow')->nullable();
            $table->string('headline')->nullable();
            $table->string('subheadline')->nullable();
            $table->text('body')->nullable();
            $table->string('image')->nullable();
            $table->string('image_alt')->nullable();
            $table->string('cta_text')->nullable();
            $table->string('cta_url')->nullable();
            $table->json('metadata')->nullable();
            $table->boolean('is_visible')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['page_id', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sections');
    }
};
