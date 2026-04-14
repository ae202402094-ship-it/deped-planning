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
            
            if (!Schema::hasColumn('schools', 'no_of_chairs')) {
                $table->integer('no_of_chairs')->default(0)->after('no_of_classrooms');
            }
            
            if (!Schema::hasColumn('schools', 'classroom_shortage')) {
                $table->integer('classroom_shortage')->default(0)->after('with_internet'); 
            }
            
            if (!Schema::hasColumn('schools', 'chair_shortage')) {
                $table->integer('chair_shortage')->default(0)->after('classroom_shortage');
            }
            
            if (!Schema::hasColumn('schools', 'toilet_shortage')) {
                $table->integer('toilet_shortage')->default(0)->after('chair_shortage');
            }

            if (!Schema::hasColumn('schools', 'with_potable_water')) {
                $table->boolean('with_potable_water')->default(false)->after('with_electricity');
            }
            
            if (!Schema::hasColumn('schools', 'with_internet')) {
                $table->boolean('with_internet')->default(false)->after('with_potable_water');
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
                'no_of_chairs', 
                'classroom_shortage', 
                'chair_shortage', 
                'toilet_shortage',
                'with_potable_water',
                'with_internet'
            ]);
        });
    }
};