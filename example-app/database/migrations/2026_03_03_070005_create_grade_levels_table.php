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
        Schema::create('grade_levels', function (Blueprint $table) {
        $table->id();
        $table->string('level_name'); // e.g., Grade 7
        $table->integer('section_count');
        $table->integer('male_count');
        $table->integer('female_count');
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grade_levels');
    }
};
