<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('schools', function (Blueprint $table) {
        $table->integer('teacher_shortage')->default(0)->after('toilet_shortage');
    });
}

public function down()
{
    Schema::table('schools', function (Blueprint $table) {
        $table->dropColumn('teacher_shortage');
    });
}
};
