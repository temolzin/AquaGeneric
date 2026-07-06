<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDiscountFieldsToDebtsAndPaymentsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('debts', 'has_discount')) {
            Schema::table('debts', function (Blueprint $table) {
                $table->boolean('has_discount') ->default(false) ->after('amount');
            });
        }

        if (!Schema::hasColumn('debts', 'discount_id')) {
            Schema::table('debts', function (Blueprint $table) {
                $table->unsignedBigInteger('discount_id') ->nullable() ->after('has_discount');
                $table->foreign('discount_id') ->references('id') ->on('discounts') ->nullOnDelete();
            });
        }

        if (!Schema::hasColumn('payments', 'discount_amount')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->boolean('has_discount')->default(false)->after('amount');
                $table->unsignedBigInteger('discount_id')->nullable()->after('has_discount');
                $table->decimal('discount_percentage', 5, 2)->default(0)->after('discount_id');
                $table->decimal('discount_amount', 10, 2)->default(0)->after('discount_percentage');
                $table->decimal('final_amount', 10, 2)->default(0)->after('discount_amount');
                $table->foreign('discount_id')->references('id')->on('discounts')->nullOnDelete();
            });
        }
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'discount_id')) {
                $table->dropForeign(['discount_id']);
            }

            if (Schema::hasColumn('payments', 'final_amount')) {
                $table->dropColumn('final_amount');
            }

            if (Schema::hasColumn('payments', 'discount_percentage')) {
                $table->dropColumn('discount_percentage');
            }

            if (Schema::hasColumn('payments', 'discount_amount')) {
                $table->dropColumn('discount_amount');
            }

            if (Schema::hasColumn('payments', 'discount_id')) {
                $table->dropColumn('discount_id');
            }

            if (Schema::hasColumn('payments', 'has_discount')) {
                $table->dropColumn('has_discount');
            }
        });

        Schema::table('debts', function (Blueprint $table) {

            if (Schema::hasColumn('debts', 'discount_id')) {
                $table->dropForeign(['discount_id']);
                $table->dropColumn('discount_id');
            }

            if (Schema::hasColumn('debts', 'has_discount')) {
                $table->dropColumn('has_discount');
            }
        });
    }
}