<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUseNewReportDesignToLocalitiesTable extends Migration
{
    public function up()
    {
        Schema::table('localities', function (Blueprint $table) {
            $table->boolean('use_new_report_design')->default(false)->after('openpay_enabled');
        });
    }

    public function down()
    {
        Schema::table('localities', function (Blueprint $table) {
            $table->dropColumn('use_new_report_design');
        });
    }
}
