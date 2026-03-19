<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddPeriodColumnsAndServiceKeyToDebts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('debts', function (Blueprint $table) {
            $table->tinyInteger('period_month')->nullable()->after('end_date');
            $table->smallInteger('period_year')->nullable()->after('period_month');
        });

        // Backfill period_month and period_year from start_date where possible
        DB::statement("UPDATE debts SET period_month = MONTH(start_date), period_year = YEAR(start_date) WHERE start_date IS NOT NULL");

        // Ensure service category exists
        $service = DB::table('debt_categories')->where('name', 'Servicio de Agua')->first();
        if (!$service) {
            $serviceId = DB::table('debt_categories')->insertGetId([
                'name' => 'Servicio de Agua',
                'description' => 'Categoría por defecto para Servicio de Agua',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $serviceId = $service->id;
        }

        // Add a generated column that builds a key only for the service category, then enforce uniqueness
        // MySQL generated column expression concatenates water_connection_id, year and month
        try {
            DB::statement(
                "ALTER TABLE debts ADD COLUMN service_period_key VARCHAR(255) GENERATED ALWAYS AS (CASE WHEN debt_category_id = $serviceId THEN CONCAT(water_connection_id,'_',period_year,'_',period_month) ELSE NULL END) VIRTUAL"
            );

            DB::statement("CREATE UNIQUE INDEX ux_debts_service_period ON debts (service_period_key)");
        } catch (\Exception $e) {
            // In some environments alter/indices may fail due to privileges or engine; ignore to allow migration to continue.
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        try {
            DB::statement('DROP INDEX ux_debts_service_period ON debts');
        } catch (\Exception $e) {
        }

        try {
            DB::statement('ALTER TABLE debts DROP COLUMN service_period_key');
        } catch (\Exception $e) {
        }

        Schema::table('debts', function (Blueprint $table) {
            if (Schema::hasColumn('debts', 'period_month')) {
                $table->dropColumn('period_month');
            }
            if (Schema::hasColumn('debts', 'period_year')) {
                $table->dropColumn('period_year');
            }
        });
    }
}
