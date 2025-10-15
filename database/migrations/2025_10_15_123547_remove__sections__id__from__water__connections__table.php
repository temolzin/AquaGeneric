<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveSectionsIdFromWaterConnectionsTable extends Migration
{
    public function up()
    {
        Schema::table('water_connections', function (Blueprint $table) {
            if (Schema::hasColumn('water_connections', 'sections_id')) {
                $table->dropForeign(['sections_id']);
                $table->dropColumn('sections_id');
            }
        });
    }

    public function down()
    {
        Schema::table('water_connections', function (Blueprint $table) {
            $table->unsignedBigInteger('sections_id')->nullable();
            $table->foreign('sections_id')->references('id')->on('sections')->onDelete('set null');
        });
    }
}