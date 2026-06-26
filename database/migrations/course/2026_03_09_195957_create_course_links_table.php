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
        Schema::create('course_links', function (Blueprint $table) {
            $table->id();

            $table->foreignId('course_id')->constrained()->cascadeOnDelete();

            $table->string('title');
            $table->text('url');
            $table->string('description');

            $table->enum('type', ['live', 'resource', 'external']);
            $table->string('platform')->nullable(); // 'google-meet', 'github', 'docs'

            $table->timestamp('expires_at')->nullable()->index(); // expired link meting 24H / and to make it cron job to delete the ended
//            $table->enum('status', ['active', 'expired'])->default('active')->index();

            $table->timestamps();
            $table->softDeletes();
            $table->index(['course_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_links');
    }
};
