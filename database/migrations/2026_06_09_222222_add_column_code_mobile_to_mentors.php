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
        Schema::table('mentors', function (Blueprint $table) {
            $table->string('code_mobile')->nullable()->after('mobile_number'); // for the code of the mobile number like +20 for Egypt and +1 for USA and any other code for the country
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mentors', function (Blueprint $table) {
            $table->dropColumn('code_mobile');
        });
    }
};
