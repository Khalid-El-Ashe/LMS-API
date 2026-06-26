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
        Schema::create('mentors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username')->unique();
            $table->string('slug')->unique();
            $table->string('profile_image')->nullable();
            $table->string('email');
            $table->string('mobile_number');
            $table->string('address');
            $table->string('city');
            $table->string('state');
            $table->integer('experience');
            $table->string('nationality');
            $table->json('status'); // 'mentoring', 'opportunities', 'financial_support'

            $table->json('files')->nullable(); // cv, image payment, and any other files that the mentor want to upload
            $table->boolean('is_active')->default(false); // is about activation mentor account

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
        Schema::dropIfExists('mentors');
    }
};
