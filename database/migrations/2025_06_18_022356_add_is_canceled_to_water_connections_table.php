<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsCanceledToWaterConnectionsTable extends Migration
{
    public function up()
    {
        Schema::table('water_connections', function (Blueprint $table) {
            $table->boolean('is_canceled')->default(false)->before('canceled_at');
        });
    }

    public function down()
    {
        Schema::table('water_connections', function (Blueprint $table) {
            $table->dropColumn('is_canceled');
        });
    }
}
