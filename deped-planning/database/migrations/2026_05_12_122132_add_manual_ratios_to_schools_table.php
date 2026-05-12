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
            if (!Schema::hasColumn('schools', 'teacher_ratio')) {
                $table->string('teacher_ratio')->nullable();
            }
            if (!Schema::hasColumn('schools', 'classroom_ratio')) {
                $table->string('classroom_ratio')->nullable();
            }
            if (!Schema::hasColumn('schools', 'chair_ratio')) {
                $table->string('chair_ratio')->nullable();
            }
            if (!Schema::hasColumn('schools', 'toilet_ratio')) {
                $table->string('toilet_ratio')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->dropColumn([
                'teacher_ratio', 
                'classroom_ratio', 
                'chair_ratio', 
                'toilet_ratio'
            ]);
        });
    }
};