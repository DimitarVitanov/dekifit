<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('content')->nullable();
            $table->string('category');
            $table->string('featured_image')->nullable();
            $table->string('featured_image_alt')->nullable();
            $table->integer('prep_time')->nullable();
            $table->integer('cook_time')->nullable();
            $table->integer('servings')->default(1);
            $table->integer('calories')->nullable();
            $table->integer('protein')->nullable();
            $table->integer('carbs')->nullable();
            $table->integer('fat')->nullable();
            $table->json('ingredients')->nullable();
            $table->json('instructions')->nullable();
            $table->json('tags')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};
