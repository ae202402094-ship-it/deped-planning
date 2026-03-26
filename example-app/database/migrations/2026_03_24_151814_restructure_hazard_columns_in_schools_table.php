<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            // 1. Delete the old columns seen in your screenshot
            if (Schema::hasColumn('schools', 'hazard_landslide')) {
                $table->dropColumn(['hazard_landslide', 'hazard_flood', 'hazard_traffic']);
            }

            // 2. Add the new modular columns
            // hazard_type: Stores 'Landslide', 'Flood', or Custom Text from "Others"
            $table->string('hazard_type')->default('None')->after('no_of_toilets');
            
            // hazard_level: Stores 'None', 'Moderate', 'High'
            $table->string('hazard_level')->default('None')->after('hazard_type');
        });
    }

    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            // Rollback: Remove the new and put back the old (optional but good practice)
            $table->dropColumn(['hazard_type', 'hazard_level']);
            $table->string('hazard_landslide')->default('None');
            $table->string('hazard_flood')->default('None');
            $table->string('hazard_traffic')->default('None');
        });
    }
};