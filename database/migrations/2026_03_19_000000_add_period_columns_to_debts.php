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
            // Campos normalizados para consultas eficientes
            $table->tinyInteger('period_month')->nullable()->after('end_date');
            $table->smallInteger('period_year')->nullable()->after('period_month');

            // Índice útil real (para búsquedas y validaciones)
            $table->index(
                ['water_connection_id', 'period_year', 'period_month'],
                'idx_debts_period'
            );
        });

        // Backfill: llenar datos existentes
        DB::statement("
            UPDATE debts 
            SET 
                period_month = MONTH(start_date),
                period_year = YEAR(start_date)
            WHERE start_date IS NOT NULL
        ");
    }

    public function down()
    {
        Schema::table('debts', function (Blueprint $table) {
            // Eliminar índice primero
            $table->dropIndex('idx_debts_period');

            // Luego columnas
            $table->dropColumn(['period_month', 'period_year']);
        });
    }
}
