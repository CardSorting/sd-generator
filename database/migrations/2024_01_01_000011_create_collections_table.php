<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('collections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_public')->default(true);
            $table->timestamps();
        });

        Schema::create('collection_image_generation', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collection_id')->constrained()->onDelete('cascade');
            $table->foreignId('image_generation_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            // Ensure an image can only be in a collection once
            $table->unique(['collection_id', 'image_generation_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('collection_image_generation');
        Schema::dropIfExists('collections');
    }
};
