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
        Schema::create('task_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();

            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');

            $table->integer('grade')->nullable();
            $table->text('answer')->nullable();
            $table->string('file')->nullable();

            $table->foreignId('reviewed_by')->nullable()->constrained('mentors')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();

            $table->text('review_notes')->nullable();

            $table->timestamps();

            $table->unique(['task_id', 'student_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_submissions');
    }
};
