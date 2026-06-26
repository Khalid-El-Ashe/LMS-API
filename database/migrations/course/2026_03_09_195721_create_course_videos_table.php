<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('course_videos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();

            $table->string('title');
            $table->string('youtube_id');
            $table->text('description')->nullable();
            $table->integer('duration')->nullable();
            $table->string('thumbnail')->nullable();

            $table->unsignedInteger('order')->index(); // this is for indexs
            $table->unique(['course_id', 'youtube_id']);

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_videos');
    }
};
