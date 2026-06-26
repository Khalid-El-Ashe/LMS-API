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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('email');
            $table->string('username')->unique();
            $table->string('full_name');
            $table->string('profile_image')->nullable();
            $table->string('university_name');
            $table->string('university_major');
            $table->string('mobile_number'); // starting with WhatsApp
            $table->string('teacher_collage')->nullable();

            // $table->foreignId('course_id')->nullable()->constrained('courses')->nullOnDelete(); // i need to replace this column with the pivot table course_students because the student can be enrolled in many courses and the course can have many students

            $table->json('files')->nullable(); // cv, image payment, and any other files that the student want to upload
            $table->boolean('is_active')->default(false); // is about student is Course fees payment
            $table->enum('gender', ['male', 'female']);
            $table->softDeletes();
            $table->timestamp('last_active_at')->nullable(); // for tracking the last activity of the student is have a middleware to update this column with every request(outomatically update the last activity time with every request)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
