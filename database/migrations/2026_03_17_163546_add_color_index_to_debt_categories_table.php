<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColorIndexToDebtCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('debt_categories', function (Blueprint $table) {
            $table->string('color')->default('#6c757d')->after('description');
        });
    }

    public function down()
    {
        Schema::table('debt_categories', function (Blueprint $table) {
            $table->dropColumn('color');
        });
    }
}
