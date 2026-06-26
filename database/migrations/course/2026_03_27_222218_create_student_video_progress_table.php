<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * this table is for tracking the progress of a student in a video, it will have the student_id, video_id, is_completed, completed_at, and timestamps, and it will have a unique constraint on student_id and video_id to prevent duplicate entries for the same student and video
     */
    public function up(): void
    {
        Schema::create('student_video_progress', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('video_id')->constrained('course_videos')->cascadeOnDelete();

            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();

            $table->integer('last_position')->default(0);
            $table->integer('watched_seconds')->default(0);

            $table->timestamps();

            $table->unique(['student_id', 'video_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_video_progress');
    }
};
