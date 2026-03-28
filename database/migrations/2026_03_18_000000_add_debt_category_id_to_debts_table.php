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
        $default = DB::table('debt_categories')->where('name', 'Servicio de Agua')->first();
        if (!$default) {
            // find a user with Supervisor or Secretaria role
            $createdBy = DB::table('users')
                ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->whereIn('roles.name', ['Supervisor', 'Secretaria'])
                ->value('users.id');

            // If no user with the required roles exists yet, insert category with null created_by (column is nullable)
            $defaultId = DB::table('debt_categories')->insertGetId([
                'name' => 'Servicio de Agua',
                'description' => 'Categoría por defecto para Servicio de Agua',
                'created_by' => $createdBy,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $defaultId = $default->id;
        }

        Schema::table('debts', function (Blueprint $table) {
            $table->unsignedBigInteger('debt_category_id')->nullable()->after('created_by');
        });

        DB::table('debts')->update(['debt_category_id' => $defaultId]);

        Schema::table('debts', function (Blueprint $table) {
            try {
                $table->foreign('debt_category_id')->references('id')->on('debt_categories')->onDelete('restrict');
            } catch (Exception $e) {
                // ignore FK creation failures in environments missing DB privileges
            }
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
            if (Schema::hasColumn('debts', 'debt_category_id')) {
                $table->dropForeign(['debt_category_id']);
                $table->dropColumn('debt_category_id');
            }
        });
    }
}
