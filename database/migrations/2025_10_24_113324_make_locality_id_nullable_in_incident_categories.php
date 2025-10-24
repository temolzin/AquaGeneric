<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class MakeLocalityIdNullableInIncidentCategories extends Migration
{
    public function up()
    {
        DB::statement('ALTER TABLE incident_categories MODIFY COLUMN locality_id BIGINT UNSIGNED NULL');
    }

    public function down()
    {
        DB::table('incident_categories')
            ->whereNull('locality_id')
            ->update(['locality_id' => 1]);

        DB::statement('ALTER TABLE incident_categories MODIFY COLUMN locality_id BIGINT UNSIGNED NOT NULL');
    }
}
