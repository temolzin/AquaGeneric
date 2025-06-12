<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class RenameCancellationReasonColumnInWaterConnectionsTable extends Migration
{
    public function up()
    {
        DB::statement('ALTER TABLE water_connections CHANGE cancellation_reason cancel_description TEXT NULL');
    }

    public function down()
    {
        DB::statement('ALTER TABLE water_connections CHANGE cancel_description cancellation_reason TEXT NULL');
    }
}
