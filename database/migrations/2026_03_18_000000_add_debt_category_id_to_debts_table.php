<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDebtCategoryIdToDebtsTable extends Migration
{
    public function up()
    {
        Schema::table('debts', function (Blueprint $table) {
            $table->unsignedBigInteger('debt_category_id')
                ->nullable()
                ->after('created_by');

            $table->foreign('debt_category_id')
                ->references('id')
                ->on('debt_categories')
                ->onDelete('restrict');
        });
    }

    public function down()
    {
        Schema::table('debts', function (Blueprint $table) {
            $table->dropForeign(['debt_category_id']);
            $table->dropColumn('debt_category_id');
        });
    }
}
