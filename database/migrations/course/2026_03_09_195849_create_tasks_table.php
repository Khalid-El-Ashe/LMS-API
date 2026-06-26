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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('video_id')->nullable()->constrained('course_videos')->cascadeOnDelete();

            $table->string('title');
            $table->text('description')->nullable();
//            $table->boolean('is_required')->default(false); // this is for look vide to sign the task and next video

//            $table->enum('type', ['file', 'link', 'text'])->default('file'); // in here the task is ben file or link (GitHub or Google form)

//            $table->enum('type', ['video', 'reading', 'quiz'])->default('video');
//            $table->enum('submission_type', ['file', 'link', 'text'])->nullable(); // in here the task is ben file or link (GitHub or Google form)

            $table->integer('passing_grade')->nullable(); # Quiz Laravel Basics 80


            // order column to order the tasks in the video
            $table->unsignedInteger('order')->index()->default(0);

            $table->softDeletes();
            $table->timestamps();

            $table->index(['course_id', 'video_id']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
