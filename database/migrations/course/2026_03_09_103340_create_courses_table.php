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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('description')->nullable();

            $table->string('logo')->nullable();
            $table->string('slug')->unique();
            $table->string('youtube_playlist_url');

            // $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->decimal('price', 8, 2)->nullable();
            $table->decimal('rating', 3, 2)->default(0)->nullable();

//            $table->boolean('certificate_enabled')->default(false);

            $table->timestamp('last_synced_at')->nullable(); // To track when the course was last synced with YouTube (Cron Job)
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
