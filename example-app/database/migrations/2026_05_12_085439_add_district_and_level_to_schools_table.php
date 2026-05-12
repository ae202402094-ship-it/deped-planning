<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            // Using enum ensures only these exact values are accepted
            $table->enum('school_level', ['Primary', 'Secondary'])->after('name')->nullable();
            
            // District string (e.g., 'Baliwasan', 'Tetuan', 'Ayala', 'Curuan')
            $table->string('district')->after('school_level')->nullable();

            // Sector enum for Public or Private classification
            $table->enum('sector', ['Public', 'Private'])->after('district')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->dropColumn(['school_level', 'district', 'sector']);
        });
    }
};