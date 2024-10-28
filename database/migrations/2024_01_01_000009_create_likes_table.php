<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('image_generation_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            // Ensure a user can only like an image once
            $table->unique(['user_id', 'image_generation_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('likes');
    }
};
