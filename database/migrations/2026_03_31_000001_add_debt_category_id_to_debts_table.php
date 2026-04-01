<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddDebtCategoryIdToDebtsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('debts', function (Blueprint $table) {
            $table->unsignedBigInteger('debt_category_id')->nullable()->after('id');
        });

        // ensure global service category exists and backfill existing debts
        $service = DB::table('debt_categories')->where('name', 'Servicio de Agua')->first();
        if (! $service) {
            $serviceId = DB::table('debt_categories')->insertGetId([
                'name' => 'Servicio de Agua',
                'description' => 'Categoría global para Servicio de Agua',
                'color' => '#007bff',
                'locality_id' => null,
                'created_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $serviceId = $service->id;
        }

        DB::table('debts')->whereNull('debt_category_id')->update(['debt_category_id' => $serviceId]);

        Schema::table('debts', function (Blueprint $table) {
            $table->foreign('debt_category_id')->references('id')->on('debt_categories')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('debts', function (Blueprint $table) {
            $table->dropForeign(['debt_category_id']);
            $table->dropColumn('debt_category_id');
        });
    }
}
