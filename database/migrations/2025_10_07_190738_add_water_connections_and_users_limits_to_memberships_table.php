<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWaterConnectionsAndUsersLimitsToMembershipsTable extends Migration
{
    public function up()
    {
        Schema::table('memberships', function (Blueprint $table) {
            $table->integer('water_connections_number')->default(0)->after('term_months');
            $table->integer('users_number')->default(0)->after('water_connections_number');
        });
    }

    public function down()
    {
        Schema::table('memberships', function (Blueprint $table) {
            $table->dropColumn(['water_connections_number', 'users_number']);
        });
    }
}
