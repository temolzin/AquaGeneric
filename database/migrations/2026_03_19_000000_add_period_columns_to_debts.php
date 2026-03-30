<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddPeriodColumnsToDebts extends Migration
{
    public function up()
    {
        Schema::table('debts', function (Blueprint $table) {
            $table->tinyInteger('period_month')->nullable()->after('end_date');
            $table->smallInteger('period_year')->nullable()->after('period_month');
            $table->index(
                ['water_connection_id', 'period_year', 'period_month'],
                'idx_debts_period'
            );
        });

        DB::statement("
            UPDATE debts
            SET period_month = MONTH(start_date),
                period_year = YEAR(start_date)
            WHERE start_date IS NOT NULL
        ");
    }

    public function down()
    {
        Schema::table('debts', function (Blueprint $table) {
            $table->dropIndex('idx_debts_period');
            $table->dropColumn(['period_month', 'period_year']);
        });
    }
}
