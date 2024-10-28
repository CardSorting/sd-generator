<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sd_models', function (Blueprint $table) {
            $table->id();
            $table->string('title')->unique();
            $table->text('description')->nullable();
            $table->string('preview_image')->nullable();
            $table->string('category'); // base, anime, realistic, etc.
            $table->string('style_type'); // general, character, landscape, etc.
            $table->integer('recommended_steps')->default(20);
            $table->float('recommended_cfg')->default(7.0);
            $table->text('example_prompt')->nullable();
            $table->timestamps();
        });

        Schema::create('model_tags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sd_model_id')->constrained()->onDelete('cascade');
            $table->foreignId('tag_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['sd_model_id', 'tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('model_tags');
        Schema::dropIfExists('sd_models');
    }
};
