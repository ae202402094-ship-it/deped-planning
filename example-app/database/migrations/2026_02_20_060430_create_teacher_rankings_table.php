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
    Schema::create('teacher_rankings', function (Blueprint $table) {
        $table->id();
        $table->string('career_stage');
        $table->string('position_title');
        $table->integer('salary_grade');
        $table->integer('teacher_count')->default(0);
        $table->timestamps();
    });
}

        

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_rankings');
    }
};
