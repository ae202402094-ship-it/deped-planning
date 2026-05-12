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
        // Infrastructure Booleans
        $table->boolean('with_electricity')->default(false)->after('no_of_toilets');
        $table->boolean('with_potable_water')->default(false)->after('with_electricity');
        $table->boolean('with_internet')->default(false)->after('with_potable_water');

        // Shortage Quantities
        $table->integer('classroom_shortage')->default(0)->after('with_internet');
        $table->integer('chair_shortage')->default(0)->after('classroom_shortage');
        $table->integer('toilet_shortage')->default(0)->after('chair_shortage');

        // Hazards Text
        $table->text('hazards')->nullable()->after('toilet_shortage');
    });
}

public function down(): void
{
    Schema::table('schools', function (Blueprint $table) {
        $table->dropColumn([
            'with_electricity', 
            'with_potable_water', 
            'with_internet', 
            'classroom_shortage', 
            'chair_shortage', 
            'toilet_shortage', 
            'hazards'
        ]);
    });
}
};
