<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();

            $table->morphs('user'); // Mentor or Student

//            $table->morphs('commentable'); // Video or Task
            $table->foreignId('course_video_id')->constrained('course_videos')->cascadeOnDelete();
            $table->string('body', 150);

            $table->timestamps();
        });
    }

    // /**
    //  * Reverse the migrations.
    //  */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
