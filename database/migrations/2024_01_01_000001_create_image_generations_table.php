<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('image_generations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('prompt');
            $table->string('image_url');
            $table->string('thumbnail_url');
            $table->string('status');
            $table->json('settings')->nullable();
            $table->integer('credits_used');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('image_generations');
    }
};
