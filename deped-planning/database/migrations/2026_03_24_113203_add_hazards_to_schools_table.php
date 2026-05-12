<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            // Stores: 'None', 'Landslide', 'Flood', 'Traffic', or custom text from "Others"
            $table->string('hazard_type')->default('None')->after('no_of_toilets');
            
            // Stores: 'None', 'Moderate', 'High'
            $table->string('hazard_level')->default('None')->after('hazard_type');
        });
    }

    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->dropColumn(['hazard_type', 'hazard_level']);
        });
    }
};