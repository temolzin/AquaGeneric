<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ReplaceTypeWithExpenseTypeIdInGeneralExpenses extends Migration
{
    public function up()
    {
        Schema::table('general_expenses', function (Blueprint $table) {
            $table->dropColumn('type');
            
            $table->unsignedBigInteger('expense_type_id')->nullable()->after('amount');
            
            $table->foreign('expense_type_id')
                ->references('id')
                ->on('expense_types')
                ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('general_expenses', function (Blueprint $table) {
            $table->dropForeign(['expense_type_id']);
            $table->dropColumn('expense_type_id');
            $table->enum('type', ['mainteinence', 'services', 'supplies', 'taxes', 'staff'])->nullable();
        });
    }
}
