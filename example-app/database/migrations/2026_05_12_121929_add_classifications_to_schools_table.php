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
        Schema::table('schools', function (Blueprint $table) {
            // Check if columns don't exist before adding them to prevent crash
            if (!Schema::hasColumn('schools', 'sector')) {
                $table->enum('sector', ['Public', 'Private'])->nullable()->after('name');
            }
            if (!Schema::hasColumn('schools', 'school_level')) {
                $table->enum('school_level', ['Primary', 'Secondary'])->nullable()->after('sector');
            }
            if (!Schema::hasColumn('schools', 'district')) {
                $table->string('district')->nullable()->after('school_level');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->dropColumn(['sector', 'school_level', 'district']);
        });
    }
};