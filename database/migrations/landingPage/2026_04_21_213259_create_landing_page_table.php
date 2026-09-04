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
        Schema::create('landing_page', function (Blueprint $table) {
            $table->id();
            $table->integer('graduates_count')->default(0); # عدد الخريجين
            $table->integer('courses_count')->default(0); # عدد الدورات
            $table->integer('professional_trainer_count')->default(0); # عدد المدربين
            $table->integer('success_stories_count')->default(0); # عدد قصص النجاح
            $table->integer('practical_projects_count')->default(0); # عدد المشاريع العملية
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landing_page');
    }
};
