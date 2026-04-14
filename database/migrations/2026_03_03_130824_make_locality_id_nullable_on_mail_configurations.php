<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class MakeLocalityIdNullableOnMailConfigurations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE mail_configurations MODIFY locality_id BIGINT UNSIGNED NULL');
    }

    public function down()
    {
        // Update any NULL values before making the column NOT NULL
        DB::table('mail_configurations')->whereNull('locality_id')->delete();

        DB::statement('ALTER TABLE mail_configurations MODIFY locality_id BIGINT UNSIGNED NOT NULL');
    }
}
