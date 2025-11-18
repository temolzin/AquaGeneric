<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::statement('ALTER TABLE incident_statuses MODIFY locality_id BIGINT UNSIGNED NULL');
    }

    public function down()
    {
        DB::statement('UPDATE incident_statuses SET locality_id = 1 WHERE locality_id IS NULL');
        DB::statement('ALTER TABLE incident_statuses MODIFY locality_id BIGINT UNSIGNED NOT NULL');
    }
};
