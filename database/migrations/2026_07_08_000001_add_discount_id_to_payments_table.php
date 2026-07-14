<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDiscountIdToPaymentsTable extends Migration
{
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->unsignedBigInteger('discount_id')->nullable();
            $table->decimal('discount_amount', 8, 2)->default(0);

            $table->foreign('discount_id')->references('id')->on('discounts')->onDelete('set null');
        });
    }

    public function down()
    {
        if (Schema::hasColumn('payments', 'discount_id')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->dropForeign(['discount_id']);
                $table->dropColumn(['discount_id','discount_amount',]);
            });
        }
    }
}
