<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('incident_statuses', function (Blueprint $table) {
            $table->string('color')->default('#6c757d')->after('description');
        });
    }

    public function down()
    {
        Schema::table('incident_statuses', function (Blueprint $table) {
            $table->dropColumn('color');
        });
    }
};
