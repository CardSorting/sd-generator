<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('type'); // style, mood, subject, etc.
            $table->text('description')->nullable();
            $table->string('color', 7)->default('#6B7280'); // hex color
            $table->timestamps();
        });

        Schema::create('image_generation_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('image_generation_id')->constrained()->onDelete('cascade');
            $table->foreignId('tag_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['image_generation_id', 'tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('image_generation_tag');
        Schema::dropIfExists('tags');
    }
};
